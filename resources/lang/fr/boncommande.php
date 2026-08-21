<?php

return [
    // Nouns
    'bon_commandes' => 'Bons de commande',
    'bon_commande' => 'Bon de commande',
    'devis' => 'Devis',
    'reference' => 'Référence',
    'customer' => 'Client',
    'date' => 'Date',
    'total' => 'Total',
    'note' => 'Note',
    'details' => 'Détails',
    'document_chain' => 'Chaîne des documents',

    // Info blocks
    'company_info' => 'Infos société',
    'customer_info' => 'Infos client',
    'order_info' => 'Infos commande',
    'email' => 'E-mail',
    'phone' => 'Téléphone',
    'net_unit_price' => 'Prix unitaire net',
    'quantity' => 'Quantité',
    'discount' => 'Remise',
    'tax' => 'TVA',
    'sub_total' => 'Sous-total',
    'shipping' => 'Livraison',
    'grand_total' => 'Total général',

    // Actions
    'edit' => 'Modifier',
    'edit_bon_commande' => 'Modifier le bon de commande',
    'update' => 'Mettre à jour',
    'confirm' => 'Confirmer',
    'cancel' => 'Annuler',
    'delete' => 'Supprimer',
    'convert-to-commande' => 'Transformer en commande',
    'transform-to-bon-commande' => 'Transformer en bon de commande',
    'view-bon-commande' => 'Voir le bon de commande',
    'none-found' => 'Aucun bon de commande trouvé',

    // Statuses
    'status_label' => 'Statut',
    'status_draft' => 'Brouillon',
    'status_confirmed' => 'Confirmé',
    'status_converted' => 'Converti',
    'status_cancelled' => 'Annulé',

    // Flash / exception messages
    'created-from-quotation' => 'Bon de commande :reference créé à partir du devis.',
    'already-converted' => 'Le devis :reference a déjà été transformé en bon de commande.',
    'cannot-confirm-cancelled' => 'Un bon de commande annulé ne peut pas être confirmé.',
    'only-draft-confirmable' => 'Seul un bon de commande en brouillon peut être confirmé.',
    'cannot-cancel-converted' => 'Un bon de commande déjà transformé en commande ne peut pas être annulé.',
    'only-draft-editable' => 'Seul un bon de commande en brouillon peut être modifié.',
    'updated' => 'Bon de commande mis à jour.',
    'confirmed' => 'Bon de commande confirmé.',
    'cancelled' => 'Bon de commande annulé.',
    'deleted' => 'Bon de commande supprimé.',
];
