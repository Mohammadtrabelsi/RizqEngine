#!/usr/bin/env bash
#
# devis.sh — Interface d'entrée (bash) pour les DEVIS
#
# Statut : PRÉPARATION RAPIDE / EN ATTENTE
# ----------------------------------------------------------------------
# Ce script est un squelette destiné à saisir un devis depuis le terminal.
# La logique métier (persistance, appel à l'application Laravel RizqEngine,
# validation complète) reste à brancher.
#
# Modèles concernés côté application : Quotation / QuotationDetails
#
# Utilisation prévue :
#   ./scripts/devis.sh
# ----------------------------------------------------------------------

set -euo pipefail

APP_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

# --- En-tête -----------------------------------------------------------
afficher_entete() {
    clear
    echo "=========================================="
    echo "   RizqEngine — Saisie d'un DEVIS"
    echo "=========================================="
    echo
}

# --- Saisie des champs (préparation) -----------------------------------
saisir_devis() {
    read -rp "Client                : " client
    read -rp "Date (AAAA-MM-JJ)     : " date_devis
    read -rp "Référence             : " reference
    read -rp "Remarque              : " remarque

    echo
    echo "--- Récapitulatif (non enregistré) ---"
    echo "Client    : ${client:-}"
    echo "Date      : ${date_devis:-}"
    echo "Référence : ${reference:-}"
    echo "Remarque  : ${remarque:-}"
    echo
    echo "[EN ATTENTE] L'enregistrement du devis n'est pas encore branché."
    # TODO: brancher la persistance
    #   - via une commande artisan dédiée (ex: php artisan devis:create ...)
    #   - ou via un appel API à l'application RizqEngine
}

main() {
    afficher_entete
    saisir_devis
}

main "$@"
