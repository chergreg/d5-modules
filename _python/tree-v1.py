#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""
Script : list_tree.py
But : Lister tous les fichiers et dossiers du répertoire courant de manière récursive
      en respectant les règles spécifiées dans un fichier .gitignore (si présent),
      et en excluant toujours le dossier "node_modules".

Sortie : tree.txt dans le dossier courant.

Compatibilité : Windows / macOS / Linux

Prérequis :
    pip install pathspec

Fonctionnalités :
- Lecture automatique du .gitignore à la racine du script.
- Exclusion du dossier node_modules même si non présent dans .gitignore.
- Structure modulaire : fonctions pour chargement gitignore, scan, filtrage et écriture fichier.
- Gestion d’erreurs robuste.
- Messages d’information détaillés.
- input() final pour éviter la fermeture de la console sur double-clic.
"""

import os
from pathlib import Path
import traceback

try:
    from pathspec import PathSpec
    from pathspec.patterns import GitWildMatchPattern
except ImportError:
    print("Le module 'pathspec' n'est pas installé.")
    print("Veuillez exécuter : pip install pathspec")
    input("Appuyez sur Entrée pour quitter...")
    raise SystemExit


# ---------------------------------------------------------------------------
# Chargement du .gitignore
# ---------------------------------------------------------------------------
def load_gitignore(root: Path) -> PathSpec:
    """
    Charge le fichier .gitignore si présent, et retourne un objet PathSpec.
    Si absent, retourne une PathSpec vide.
    """
    gitignore_path = root / ".gitignore"

    if gitignore_path.exists():
        try:
            with open(gitignore_path, "r", encoding="utf-8") as f:
                patterns = f.read().splitlines()
            return PathSpec.from_lines(GitWildMatchPattern, patterns)
        except Exception as e:
            print(f"Erreur lors de la lecture de .gitignore : {e}")
            return PathSpec.from_lines(GitWildMatchPattern, [])
    else:
        return PathSpec.from_lines(GitWildMatchPattern, [])


# ---------------------------------------------------------------------------
# Filtrage
# ---------------------------------------------------------------------------
def should_ignore(path: Path, root: Path, gitignore_spec: PathSpec) -> bool:
    """
    Détermine si un fichier doit être ignoré selon :
    - .gitignore
    - dossier node_modules (toujours ignoré)
    """
    # Convertir en chemin relatif pour pathspec
    rel = path.relative_to(root).as_posix()

    # Exclusion forcée
    if "node_modules" in rel.split("/"):
        return True

    # Filtrage Git
    if gitignore_spec.match_file(rel):
        return True

    return False


# ---------------------------------------------------------------------------
# Scan récursif
# ---------------------------------------------------------------------------
def scan_directory(root: Path, gitignore_spec: PathSpec):
    """
    Parcourt récursivement le dossier root et retourne une liste
    triée de chemins relatifs à inclure.
    """
    results = []

    for current_path, dirs, files in os.walk(root):
        current_path = Path(current_path)

        # Filtre les dossiers (modifie dirs sur place pour éviter de descendre dedans)
        dirs[:] = [
            d for d in dirs
            if not should_ignore(current_path / d, root, gitignore_spec)
        ]

        # Ajoute les dossiers restants
        for d in dirs:
            path = (current_path / d)
            results.append(path.relative_to(root).as_posix())

        # Ajoute les fichiers restants
        for f in files:
            path = current_path / f
            if not should_ignore(path, root, gitignore_spec):
                results.append(path.relative_to(root).as_posix())

    return sorted(results)


# ---------------------------------------------------------------------------
# Écriture du résultat
# ---------------------------------------------------------------------------
def write_output(file_path: Path, lines):
    """Écrit la liste dans tree.txt"""
    try:
        with open(file_path, "w", encoding="utf-8") as f:
            f.write("\n".join(lines))
        print(f"Fichier généré : {file_path}")
    except Exception as e:
        print(f"Erreur lors de l'écriture du fichier : {e}")


# ---------------------------------------------------------------------------
# Main
# ---------------------------------------------------------------------------
def main():
    root = Path(os.getcwd())  # Répertoire courant
    root = root.parent        # Dossier parent
    
    output_file = root / "tree.txt"

    print(f"Analyse du dossier : {root}")

    try:
        gitignore_spec = load_gitignore(root)
        items = scan_directory(root, gitignore_spec)
        write_output(output_file, items)

        print("\nAnalyse terminée avec succès.")
    except Exception as e:
        print("Une erreur critique s'est produite :")
        print(e)
        traceback.print_exc()

    input("\nAppuyez sur Entrée pour quitter...")


# ---------------------------------------------------------------------------

if __name__ == "__main__":
    main()
