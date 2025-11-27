#!/usr/bin/env python3
"""
Script : duplicate_and_transform_modules.py
Description :
    Ce script duplique de manière récursive des dossiers de modules et applique
    une série de renommages et de remplacements internes.

Étapes réalisées :

1. DUPLICATION DE DOSSIERS :
    - src/components/static-module => src/components/hello-world-module
    - src/modules-json/static-module => src/modules-json/hello-world-module
    - modules/StaticModule => modules/HelloWorldModule

2. RENOMMAGE DE DOSSIERS ET FICHIERS (dans les nouveaux dossiers copiés) :
    - "StaticModule" → "HelloWorldModule"
    - "static-module" → "hello-world-module"

3. REMPLACEMENT DANS LE CONTENU DES FICHIERS texte :
    Extensions traitées : .ts, .tsx, .json, .md, .css, .php

    - static_module → hello_world_module
    - static-module → hello-world_module
    - StaticModule → HelloWorldModule
    - Static Module → Hello World Module

Pré-requis :
    Aucune dépendance externe.  
    Compatible Windows / macOS / Linux.

"""

import os
import shutil
import traceback


# =========================================================
#  CONFIGURATION
# =========================================================

COPY_OPERATIONS = [
    ("../src/components/static-module", "../src/components/hello-world-module"),    
    ("../modules/StaticModule", "../modules/HelloWorldModule"),
    ("../src/icons/module-dynamic", "../src/icons/module-hello-world")
]

RENAMES = [
    ("StaticModule", "HelloWorldModule"),
    ("static-module", "hello-world-module")
]

CONTENT_REPLACEMENTS = {
    "static_module": "hello_world_module",
    "static-module": "hello-world-module",
    "StaticModule": "HelloWorldModule",
    "Static Module": "Hello World Module",
    "staticModule": "helloWorldModule",
    "example/module-static" : "example/module-hello-world",    
}

TEXT_EXTENSIONS = {".ts", ".tsx", ".json", ".md", ".scss" ".css", ".php"}


# =========================================================
#  MODULE 1 : DUPLICATION
# =========================================================

def duplicate_directories():
    print("=== Étape 1 : Duplication des répertoires ===")
    for src, dest in COPY_OPERATIONS:
        try:
            if not os.path.exists(src):
                print(f"[WARN] Source introuvable : {src}")
                continue

            if os.path.exists(dest):
                print(f"[INFO] Le dossier existe déjà, suppression avant copie : {dest}")
                shutil.rmtree(dest)

            shutil.copytree(src, dest)
            print(f"[OK] Copié : {src} → {dest}")

        except Exception as e:
            print(f"[ERREUR] Impossible de copier {src}: {e}")
            traceback.print_exc()


# =========================================================
#  MODULE 2 : RENOMMAGE RECURSIF
# =========================================================

def rename_recursively(base_path):
    """
    Renomme les dossiers et fichiers dans `base_path` selon RENAMES.
    """
    print(f"\n=== Étape 2 : Renommage récursif dans : {base_path} ===")

    try:
        for root, dirs, files in os.walk(base_path, topdown=False):
            # Renommage des fichiers
            for name in files:
                new_name = name
                for old, new in RENAMES:
                    if old in new_name:
                        new_name = new_name.replace(old, new)

                if new_name != name:
                    old_path = os.path.join(root, name)
                    new_path = os.path.join(root, new_name)
                    os.rename(old_path, new_path)
                    print(f"[FILE] {old_path} -> {new_path}")

            # Renommage des dossiers
            for name in dirs:
                new_name = name
                for old, new in RENAMES:
                    if old in new_name:
                        new_name = new_name.replace(old, new)

                if new_name != name:
                    old_path = os.path.join(root, name)
                    new_path = os.path.join(root, new_name)
                    os.rename(old_path, new_path)
                    print(f"[DIR] {old_path} -> {new_path}")

    except Exception as e:
        print(f"[ERREUR] Renommage dans {base_path} : {e}")
        traceback.print_exc()


# =========================================================
#  MODULE 3 : MODIFICATION DU CONTENU
# =========================================================

def process_text_files(base_path):
    """
    Parcourt les fichiers texte et remplace les chaînes définies.
    """
    print(f"\n=== Étape 3 : Remplacement de contenu dans : {base_path} ===")

    for root, dirs, files in os.walk(base_path):
        for file in files:
            extension = os.path.splitext(file)[1]
            if extension not in TEXT_EXTENSIONS:
                continue

            file_path = os.path.join(root, file)

            try:
                with open(file_path, "r", encoding="utf-8") as f:
                    content = f.read()

                new_content = content
                for old, new in CONTENT_REPLACEMENTS.items():
                    new_content = new_content.replace(old, new)

                if new_content != content:
                    with open(file_path, "w", encoding="utf-8") as f:
                        f.write(new_content)
                    print(f"[EDIT] Modifié : {file_path}")

            except Exception as e:
                print(f"[ERREUR] Lecture/écriture : {file_path} : {e}")
                traceback.print_exc()


# =========================================================
#  MAIN
# =========================================================

def main():
    duplicate_directories()

    # Étape 2 & 3 doivent s'appliquer aux nouveaux dossiers copiés
    for _, dest in COPY_OPERATIONS:
        if os.path.exists(dest):
            rename_recursively(dest)
            process_text_files(dest)

    print("\n=== Script terminé avec succès ===")
    input("\nAppuyez sur Entrée pour quitter...")  # Empêche la fermeture automatique


if __name__ == "__main__":
    main()
