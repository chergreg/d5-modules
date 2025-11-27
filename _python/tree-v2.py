#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""
Script : tree_graphic.py
But :
    Générer un arbre graphique du dossier courant et l'enregistrer dans tree.txt.
    Respecte automatiquement les règles du .gitignore et ignore toujours node_modules.

Sortie :
    tree.txt (format graphique)

Compatibilité :
    Windows / macOS / Linux

Prérequis :
    pip install pathspec
"""

import os
from pathlib import Path
import traceback

try:
    from pathspec import PathSpec
    from pathspec.patterns import GitWildMatchPattern
except ImportError:
    print("ERREUR : Le module 'pathspec' n'est pas installé.")
    print("Installez-le avec : pip install pathspec")
    input("Appuyez sur Entrée pour quitter...")
    raise SystemExit


# ============================================================================
# CHARGEMENT DU .gitignore
# ============================================================================
def load_gitignore(root: Path) -> PathSpec:
    gitignore_path = root / ".gitignore"

    if gitignore_path.exists():
        try:
            with open(gitignore_path, "r", encoding="utf-8") as f:
                patterns = f.read().splitlines()
            return PathSpec.from_lines(GitWildMatchPattern, patterns)
        except Exception as e:
            print(f"Erreur lecture .gitignore : {e}")
            return PathSpec.from_lines(GitWildMatchPattern, [])
    else:
        return PathSpec.from_lines(GitWildMatchPattern, [])


# ============================================================================
# FILTRAGE
# ============================================================================
def should_ignore(path: Path, root: Path, gitignore_spec: PathSpec) -> bool:
    rel = path.relative_to(root).as_posix()

    # Exclusion forcée
    if "node_modules" in rel.split("/"):
        return True

    # Exclusion via gitignore
    if gitignore_spec.match_file(rel):
        return True

    return False


# ============================================================================
# ARBRE GRAPHIQUE
# ============================================================================
def build_tree_graphic(root: Path, gitignore_spec: PathSpec):
    """
    Retourne une liste de lignes formatées en arbre graphique.
    """
    lines = []
    root_name = root.name or "Root"
    lines.append(root_name)

    def walk(directory: Path, prefix: str):
        try:
            items = sorted(directory.iterdir(), key=lambda p: (p.is_file(), p.name.lower()))
        except PermissionError:
            return

        # Filtrage
        items = [i for i in items if not should_ignore(i, root, gitignore_spec)]

        count = len(items)

        for index, item in enumerate(items):
            connector = "└─ " if index == count - 1 else "├─ "
            line = prefix + connector + item.name
            lines.append(line)

            if item.is_dir():
                new_prefix = prefix + ("   " if index == count - 1 else "│  ")
                walk(item, new_prefix)

    walk(root, "")
    return lines


# ============================================================================
# ÉCRITURE DANS LE FICHIER
# ============================================================================
def write_output(file_path: Path, lines):
    try:
        with open(file_path, "w", encoding="utf-8") as f:
            f.write("\n".join(lines))
        print(f"Arbre généré dans : {file_path}")
    except Exception as e:
        print(f"Erreur écriture : {e}")


# ============================================================================
# MAIN
# ============================================================================
def main():
    root = Path(os.getcwd())  # Répertoire courant
    root = root.parent        # Dossier parent
    
    output_file = root / "tree.txt"

    print(f"📁 Analyse du dossier : {root}")

    try:
        gitignore_spec = load_gitignore(root)
        lines = build_tree_graphic(root, gitignore_spec)
        write_output(output_file, lines)

        print("\nGénération terminée avec succès.")

    except Exception as e:
        print("\n❌ Une erreur critique est survenue :")
        print(e)
        traceback.print_exc()

    input("\nAppuyez sur Entrée pour quitter...")


# ============================================================================
if __name__ == "__main__":
    main()
