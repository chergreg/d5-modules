#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""
Script d’automatisation de duplication / transformation de module.

Fonctionnalités :
1. Mode "duplicate" (par défaut) :
   - Duplique les dossiers :
       modules/<SourcePascalCase>       -> modules/<CiblePascalCase>
       src/components/<source-kebab>    -> src/components/<cible-kebab>
       src/icons/module-<source-kebab>  -> src/icons/module-<cible-kebab>
   - Transforme le contenu et les noms de fichiers/dossiers dans les dossiers dupliqués.

2. Mode "rename" :
   - Ne duplique rien.
   - Transforme directement les dossiers existants :
       modules/<SourcePascalCase>
       src/components/<source-kebab>
       src/icons/module-<source-kebab>
   - Renomme aussi les dossiers racine (ex : modules/HelloWorldModule -> modules/DemoCardModule)

Gestion des styles de casse :
- PascalCase       : HelloWorldModule     -> DemoCardModule
- camelCase        : helloWorldModule     -> demoCardModule
- snake_case       : hello_world_module   -> demo_card_module
- kebab-case       : hello-world-module   -> demo-card-module
- Title avec espace: Hello World Module   -> Demo Card Module
- lower avec espace: hello world module   -> demo card module
- UPPER avec espace: HELLO WORLD MODULE   -> DEMO CARD MODULE
- Préfixe "module" :
    module-hello-world  -> module-demo-card
    module_hello_world  -> module_demo_card
    module hello world  -> module demo card

Logging :
- Fichier de log : <nom-script>.YYYY-MM-DD-HH-MM-SS.log
- Exemple : module_duplicator.2025-11-28-17-42-10.log

Usage :
    python script.py HelloWorldModule DemoCardModule
    python script.py HelloWorldModule DemoCardModule --mode rename
    python script.py               # par défaut : HelloWorldModule -> DemoCardModule, mode duplicate
"""

import argparse
import logging
import shutil
import sys
import re
from datetime import datetime
from pathlib import Path
from typing import Dict, List, Tuple, Optional


# Extensions de fichiers à traiter comme texte
TEXT_EXTENSIONS = {
    ".php", ".js", ".jsx", ".ts", ".tsx",
    ".css", ".scss", ".sass", ".less",
    ".html", ".htm",
    ".json", ".md", ".txt", ".yml", ".yaml"
}


# =====================================================================
# Logging
# =====================================================================

def setup_logging(script_path: Path) -> Path:
    """
    Configure le logger principal :
      - Un handler console
      - Un handler fichier : <script-name>.YYYY-MM-DD-HH-MM-SS.log
    Retourne le chemin du fichier de log.
    """
    logger = logging.getLogger(__name__)
    logger.setLevel(logging.INFO)

    timestamp = datetime.now().strftime("%Y-%m-%d-%H-%M-%S")
    log_filename = f"{script_path.stem}.{timestamp}.log"
    log_path = script_path.with_name(log_filename)

    formatter = logging.Formatter(
        fmt="%(asctime)s [%(levelname)s] %(message)s",
        datefmt="%Y-%m-%d %H:%M:%S"
    )

    # Handler fichier
    file_handler = logging.FileHandler(log_path, encoding="utf-8")
    file_handler.setFormatter(formatter)
    file_handler.setLevel(logging.INFO)

    # Handler console
    console_handler = logging.StreamHandler()
    console_handler.setFormatter(logging.Formatter("[%(levelname)s] %(message)s"))
    console_handler.setLevel(logging.INFO)

    # Éviter les doublons si jamais le script est relancé dans le même process
    if not logger.handlers:
        logger.addHandler(file_handler)
        logger.addHandler(console_handler)

    logger.info("Fichier de log : %s", log_path)
    return log_path


def get_logger() -> logging.Logger:
    """Helper pour récupérer le logger configuré."""
    return logging.getLogger(__name__)


# =====================================================================
# Gestion des styles de casse & remplacements
# =====================================================================

def tokenize_identifier(name: str) -> List[str]:
    """
    Transforme un identifiant en liste de tokens en minuscules.
    Gère :
      - PascalCase / camelCase : HelloWorldModule -> ['hello', 'world', 'module']
      - snake_case            : hello_world_module -> ['hello', 'world', 'module']
      - kebab-case            : hello-world-module -> ['hello', 'world', 'module']
    """
    if "_" in name:
        return [t.lower() for t in name.split("_") if t]
    if "-" in name:
        return [t.lower() for t in name.split("-") if t]

    # Cas PascalCase / camelCase
    parts = re.sub(r"(?<!^)(?=[A-Z])", " ", name).split()
    return [p.lower() for p in parts]


def build_variants(name: str) -> Dict[str, str]:
    """
    À partir d'un nom (quel que soit le style), génère plusieurs variantes :
      - pascal        : HelloWorldModule
      - camel         : helloWorldModule
      - snake         : hello_world_module
      - kebab         : hello-world-module
      - title         : Hello World Module
      - lower_title   : hello world module
      - upper_title   : HELLO WORLD MODULE
      - prefix_rest_* : module-hello-world / module_hello_world / module hello world (si >= 2 tokens)
    """
    tokens = tokenize_identifier(name)
    if not tokens:
        raise ValueError(f"Nom invalide : {name!r}")

    pascal = "".join(t.capitalize() for t in tokens)
    camel = tokens[0].lower() + "".join(t.capitalize() for t in tokens[1:])
    snake = "_".join(tokens)
    kebab = "-".join(tokens)

    title = " ".join(t.capitalize() for t in tokens)
    lower_title = " ".join(tokens)
    upper_title = lower_title.upper()

    # 🔥 Nouveaux formats à ajouter
    upper_snake = "_".join(tokens).upper()       # HELLO_WORLD_MODULE
    upper_kebab = "-".join(tokens).upper()       # HELLO-WORLD-MODULE
    upper_compact = "".join(t.upper() for t in tokens)  # HELLOWORLDMODULE

    variants: Dict[str, str] = {
        "pascal": pascal,
        "camel": camel,
        "snake": snake,
        "kebab": kebab,
        "title": title,
        "lower_title": lower_title,
        "upper_title": upper_title,
        "upper_snake": upper_snake,
        "upper_kebab": upper_kebab,
        "upper_compact": upper_compact,
    }


    # Cas "module-hello-world" / "module_hello_world" / "module hello world"
    # et alias type moduleHelloWorld / ModuleHelloWorld
    if len(tokens) >= 2:
        prefix = tokens[-1]       # ex: 'module'
        rest = tokens[:-1]        # ex: ['hello', 'world']
        rest_kebab = "-".join(rest)
        rest_snake = "_".join(rest)
        rest_space = " ".join(rest)

        # chemins / strings style registre
        variants["prefix_rest_kebab"] = f"{prefix}-{rest_kebab}"      # module-hello-world
        variants["prefix_rest_snake"] = f"{prefix}_{rest_snake}"      # module_hello_world
        variants["prefix_rest_space"] = f"{prefix} {rest_space}"      # module hello world

        # alias JS/TS : moduleHelloWorld / ModuleHelloWorld
        prefix_rest_camel = prefix.lower() + "".join(t.capitalize() for t in rest)
        prefix_rest_pascal = prefix.capitalize() + "".join(t.capitalize() for t in rest)

        variants["prefix_rest_camel"] = prefix_rest_camel   # moduleHelloWorld
        variants["prefix_rest_pascal"] = prefix_rest_pascal # ModuleHelloWorld

    return variants


def build_replacement_map(source_name: str, target_name: str) -> List[Tuple[str, str]]:
    """
    Construit la table de remplacement pour tous les styles de casse.
    Retourne une liste de tuples (ancien, nouveau), triés par longueur
    décroissante de la chaîne source pour éviter les collisions.
    """
    src = build_variants(source_name)
    tgt = build_variants(target_name)

    pairs: List[Tuple[str, str]] = []
    seen_old: set[str] = set()

    for key in src.keys():
        if key not in tgt:
            continue
        old = src[key]
        new = tgt[key]
        if old == new:
            continue
        if old in seen_old:
            continue
        seen_old.add(old)
        pairs.append((old, new))

    # Remplacer d'abord les chaînes les plus longues
    pairs.sort(key=lambda x: len(x[0]), reverse=True)

    logger = get_logger()
    logger.debug("Table de remplacement construite : %s", pairs)

    return pairs


def multi_replace(text: str, replacements: List[Tuple[str, str]]) -> Tuple[str, bool]:
    """
    Applique plusieurs remplacements via str.replace, en séquence, sur une chaîne.
    Retourne (nouveau_texte, changed: bool).
    """
    original = text
    for old, new in replacements:
        text = text.replace(old, new)
    return text, (text != original)


# =====================================================================
# Traitement des fichiers et dossiers
# =====================================================================

def should_process_file(path: Path) -> bool:
    """Retourne True si le fichier doit être traité comme texte."""
    return path.suffix.lower() in TEXT_EXTENSIONS


def process_file(path: Path, replacements: List[Tuple[str, str]]) -> None:
    """
    Lit le fichier, remplace les occurrences du module source par le module cible
    (tous styles de casse gérés), puis réécrit le fichier si nécessaire.
    """
    logger = get_logger()

    if not should_process_file(path):
        return

    try:
        content = path.read_text(encoding="utf-8")
    except UnicodeDecodeError:
        logger.info("[SKIP] Fichier binaire/non-UTF8 ignoré : %s", path)
        return

    new_content, changed = multi_replace(content, replacements)

    if changed:
        path.write_text(new_content, encoding="utf-8")
        logger.info("[OK] Contenu mis à jour : %s", path)


def rename_path(path: Path, replacements: List[Tuple[str, str]]) -> Path:
    """
    Renomme un fichier ou dossier si son nom contient une occurrence du module source.
    Retourne le nouveau Path (ou l'ancien si pas de changement).
    """
    logger = get_logger()
    new_name, changed = multi_replace(path.name, replacements)
    if changed:
        new_path = path.with_name(new_name)
        path.rename(new_path)
        logger.info("[RENOMMAGE] %s -> %s", path, new_path)
        return new_path
    return path


def os_walk_bottom_up(root: Path):
    """
    Wrapper autour os.walk avec topdown=False.
    Permet de renommer d'abord les éléments les plus profonds.
    """
    import os
    for dirpath, dirnames, filenames in os.walk(root, topdown=False):
        yield Path(dirpath), dirnames, filenames


def process_tree(root: Path, replacements: List[Tuple[str, str]]) -> None:
    """
    Parcourt récursivement l'arborescence à partir de `root` :
      - met à jour le contenu des fichiers
      - renomme les fichiers et sous-dossiers contenant le nom du module source
    (Ne renomme PAS le dossier root lui-même.)
    """
    logger = get_logger()
    logger.info("[SCAN] Traitement du dossier : %s", root)

    for dirpath, dirnames, filenames in os_walk_bottom_up(root):
        # 1) Fichiers : contenu + renommage
        for filename in filenames:
            file_path = dirpath / filename
            process_file(file_path, replacements)
            rename_path(file_path, replacements)

        # 2) Dossiers : renommage des sous-dossiers
        for dirname in dirnames:
            child_dir = dirpath / dirname
            rename_path(child_dir, replacements)


# =====================================================================
# Duplication des dossiers & orchestration des modes
# =====================================================================

def copy_dir(src: Path, dst: Path, label: str) -> None:
    """
    Copie un dossier entier avec logs, et erreurs lisibles si problème.
    """
    logger = get_logger()

    if not src.exists():
        raise FileNotFoundError(f"Le dossier source {label} n'existe pas : {src}")
    if dst.exists():
        raise FileExistsError(f"Le dossier cible {label} existe déjà : {dst}")

    logger.info("[COPIE] %s :", label)
    logger.info("   de   : %s", src)
    logger.info("   vers : %s", dst)
    shutil.copytree(src, dst)
    logger.info("[OK] Copie terminée pour %s", label)


def compute_paths(base_dir: Path,
                  src_variants: Dict[str, str],
                  tgt_variants: Dict[str, str]) -> Dict[str, Dict[str, Optional[Path]]]:
    """
    Calcule les chemins source et cible pour :
      - modules
      - components
      - icons (module-hello-world -> module-demo-card)

    Retourne un dict :
      {
        "modules":    {"src": Path, "dst": Path},
        "components": {"src": Path, "dst": Path},
        "icons":      {"src": Path, "dst": Path or None},
      }
    """
    modules_dir = base_dir / "modules"
    components_dir = base_dir / "src" / "components"
    icons_dir = base_dir / "src" / "icons"

    src_mod = modules_dir / src_variants["pascal"]
    dst_mod = modules_dir / tgt_variants["pascal"]

    src_comp = components_dir / src_variants["kebab"]
    dst_comp = components_dir / tgt_variants["kebab"]

    # Pour les icônes : module-hello-world => module-demo-card
    prefix_kebab_src = src_variants.get("prefix_rest_kebab")
    prefix_kebab_tgt = tgt_variants.get("prefix_rest_kebab")

    if prefix_kebab_src and prefix_kebab_tgt:
        src_icons = icons_dir / prefix_kebab_src
        dst_icons = icons_dir / prefix_kebab_tgt
    else:
        src_icons = None
        dst_icons = None

    return {
        "modules":    {"src": src_mod,  "dst": dst_mod},
        "components": {"src": src_comp, "dst": dst_comp},
        "icons":      {"src": src_icons, "dst": dst_icons},
    }


def run_duplicate_mode(paths: Dict[str, Dict[str, Optional[Path]]],
                       replacements: List[Tuple[str, str]]) -> None:
    """
    Mode 1 : duplication + transformation.
    - Copie les dossiers source vers les dossiers cibles.
    - Applique les transformations (contenu + renommages) dans les dossiers copiés.
    """
    logger = get_logger()

    for label, entry in paths.items():
        src = entry["src"]
        dst = entry["dst"]
        if src is None or dst is None:
            logger.debug("[INFO] Pas de chemin configuré pour %s, ignoré.", label)
            continue

        if not src.exists():
            logger.warning("[WARN] Dossier source inexistant pour %s, ignoré : %s", label, src)
            continue

        try:
            copy_dir(src, dst, label)
        except (FileNotFoundError, FileExistsError) as e:
            logger.error("[ERREUR] %s", e)
            continue

        process_tree(dst, replacements)


def run_rename_mode(paths: Dict[str, Dict[str, Optional[Path]]],
                       replacements: List[Tuple[str, str]]) -> None:
    """
    Mode 2 : transformation in-place (sans duplication).
    - Transforme directement les dossiers source.
    - Renomme aussi les dossiers racine selon la table de remplacement.
    """
    logger = get_logger()

    for label, entry in paths.items():
        src = entry["src"]
        if src is None:
            logger.debug("[INFO] Pas de chemin configuré pour %s, ignoré.", label)
            continue

        if not src.exists():
            logger.warning("[WARN] Dossier source inexistant pour %s, ignoré : %s", label, src)
            continue

        logger.info("[RENAME] Mode in-place pour %s : %s", label, src)
        process_tree(src, replacements)

        # Renommer le dossier racine lui-même
        new_root = rename_path(src, replacements)
        entry["dst"] = new_root  # pour info si besoin


def duplicate_module_references_in_file(path: Path, replacements: List[Tuple[str, str]]) -> None:
    """
    Dans un fichier donné, duplique les lignes contenant le module source
    en ajoutant juste en dessous une version adaptée au module cible.

    Exemple (duplicate, HelloWorldModule -> DemoEtudiantModule) :

        use MEE\Modules\HelloWorldModule\HelloWorldModule;
        use MEE\Modules\DemoEtudiantModule\DemoEtudiantModule;

        $dependency_tree->add_dependency( new HelloWorldModule() );
        $dependency_tree->add_dependency( new DemoEtudiantModule() );
    """
    logger = get_logger()

    if not path.exists():
        logger.debug("[INFO] Fichier inexistant, ignoré : %s", path)
        return

    try:
        content = path.read_text(encoding="utf-8")
    except UnicodeDecodeError:
        logger.info("[SKIP] Fichier binaire/non-UTF8 ignoré : %s", path)
        return

    lines = content.splitlines(keepends=True)
    new_lines: List[str] = []
    changed = False

    for line in lines:
        new_lines.append(line)

        # On ne duplique que les lignes qui contiennent au moins une forme du module source
        if any(old in line for (old, _new) in replacements):
            dup_line, dup_changed = multi_replace(line, replacements)
            if dup_changed and dup_line not in new_lines:
                new_lines.append(dup_line)
                changed = True

    if changed:
        path.write_text("".join(new_lines), encoding="utf-8")
        logger.info("[OK] Références dupliquées dans : %s", path)


def update_registry_files(base_dir: Path,
                          replacements: List[Tuple[str, str]],
                          mode: str) -> None:
    """
    Met à jour les fichiers "registres" globaux :
      - modules/Modules.php
      - src/index.ts
      - src/icons/index.ts

    Si mode == "duplicate" :
        - on DUPLIQUE les lignes qui référencent le module source
          en ajoutant les lignes équivalentes pour le module cible.

    Si mode == "rename" :
        - on applique un simple renommage (search/replace) dans ces fichiers.
    """
    logger = get_logger()

    modules_php = base_dir / "modules" / "Modules.php"
    src_index_ts = base_dir / "src" / "index.ts"
    icons_index_ts = base_dir / "src" / "icons" / "index.ts"

    registry_files = [modules_php, src_index_ts, icons_index_ts]

    for path in registry_files:
        if not path.exists():
            logger.debug("[INFO] Fichier registre manquant (ignoré) : %s", path)
            continue

        if mode == "duplicate":
            duplicate_module_references_in_file(path, replacements)
        else:  # mode == "rename"
            process_file(path, replacements)



# =====================================================================
# CLI (main court & configurable)
# =====================================================================

def parse_args(argv: Optional[list], default_source: str, default_target: str, default_mode: str) -> argparse.Namespace:
    parser = argparse.ArgumentParser(
        description="Duplication / transformation de module (ex : HelloWorldModule -> DemoCardModule)"
    )

    parser.add_argument(
        "source",
        nargs="?",
        default=default_source,  # valeur injectée depuis main()
        help=f'Nom du module source (par défaut : "{default_source}")'
    )
    parser.add_argument(
        "target",
        nargs="?",
        default=default_target,  # valeur injectée depuis main()
        help=f'Nom du module cible (par défaut : "{default_target}")'
    )
    parser.add_argument(
        "--mode",
        choices=["duplicate", "rename"],
        default=default_mode,    # injecté
        help='Mode : "duplicate" (par défaut) ou "rename" (transform in-place)'
    )

    return parser.parse_args(argv)



def main(argv: Optional[list] = None) -> None:
    """
    Point d'entrée principal.

    Valeurs par défaut définies ici :
    - source : HelloWorldModule
    - target : DemoCardModule
    - mode   : duplicate
    """

    # 🔹 Constantes centralisées
    DEFAULT_SOURCE = "DemoCardModule"
    DEFAULT_TARGET = "DemoCardYTModule"
    DEFAULT_MODE   = "duplicate"  # "rename" => transformation directe

    # 🚀 Setup logging
    script_path = Path(__file__).resolve()
    setup_logging(script_path)
    logger = get_logger()

    # 🎛️ Analyse des paramètres CLI
    args = parse_args(argv, DEFAULT_SOURCE, DEFAULT_TARGET, DEFAULT_MODE)

    mode = args.mode

    logger.info("=== Module Automation Script ===")
    logger.info("Source : %s", args.source)
    logger.info("Target : %s", args.target)
    logger.info("Mode   : %s", "duplicate" if mode == "duplicate" else "rename / transform-in-place")

    # 📂 Préparation
    base_dir = script_path.parent
    base_dir = base_dir.parent # Le script est dans un sous dossier

    replacements = build_replacement_map(args.source, args.target)
    src_variants = build_variants(args.source)
    tgt_variants = build_variants(args.target)
    paths = compute_paths(base_dir, src_variants, tgt_variants)

    logger.info("Chemins utilisés :")
    for label, entry in paths.items():
        logger.info(" - %-11s source : %s", f"{label}:", entry["src"])
        logger.info("   %-11s cible  : %s", "", entry["dst"])

    # 🔁 Exécution selon mode
    if mode == "duplicate":
        run_duplicate_mode(paths, replacements)
    else:
        run_rename_mode(paths, replacements)

    # 🔄 Mise à jour des fichiers registres globaux
    update_registry_files(base_dir, replacements, mode)

    logger.info("=== Terminé ===")
    logger.info("Module '%s' %s vers '%s'.",
                args.source,
                "dupliqué puis transformé" if mode == "duplicate" else "transformé directement",
                args.target)


if __name__ == "__main__":
    main()
