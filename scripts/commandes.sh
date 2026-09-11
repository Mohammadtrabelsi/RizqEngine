#!/usr/bin/env bash
#
# commandes.sh — Interface d'entrée (bash) pour les COMMANDES
#
# Statut : PRÉPARATION RAPIDE / EN ATTENTE
# ----------------------------------------------------------------------
# Ce script est un squelette destiné à saisir une commande depuis le
# terminal. La logique métier (persistance, appel à l'application Laravel
# RizqEngine, validation complète) reste à brancher.
#
# Modèles concernés côté application : Commande / CommandeDetails
#                                      (BonCommande / BonCommandeDetails)
#
# Utilisation prévue :
#   ./scripts/commandes.sh
# ----------------------------------------------------------------------

set -euo pipefail

APP_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

# --- En-tête -----------------------------------------------------------
afficher_entete() {
    clear
    echo "=========================================="
    echo "   RizqEngine — Saisie d'une COMMANDE"
    echo "=========================================="
    echo
}

# --- Saisie des champs (préparation) -----------------------------------
saisir_commande() {
    read -rp "Client / Fournisseur  : " tiers
    read -rp "Date (AAAA-MM-JJ)     : " date_commande
    read -rp "Référence             : " reference
    read -rp "Remarque              : " remarque

    echo
    echo "--- Récapitulatif (non enregistré) ---"
    echo "Tiers     : ${tiers:-}"
    echo "Date      : ${date_commande:-}"
    echo "Référence : ${reference:-}"
    echo "Remarque  : ${remarque:-}"
    echo
    echo "[EN ATTENTE] L'enregistrement de la commande n'est pas encore branché."
    # TODO: brancher la persistance
    #   - via une commande artisan dédiée (ex: php artisan commande:create ...)
    #   - ou via un appel API à l'application RizqEngine
}

main() {
    afficher_entete
    saisir_commande
}

main "$@"
