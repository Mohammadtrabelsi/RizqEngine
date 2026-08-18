<?php

return [
    // Nouns
    'commandes' => 'Commandes',
    'commande' => 'Commande',
    'facture' => 'Facture',
    'reference' => 'Référence',
    'customer' => 'Client',
    'date' => 'Date',
    'total' => 'Total',
    'note' => 'Note',
    'details' => 'Détails',

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
    'confirm' => 'Confirmer',
    'delete' => 'Supprimer',
    'generate-facture' => 'Générer la facture',
    'none-found' => 'Aucune commande trouvée',

    // Statuses
    'status_label' => 'Statut',
    'status_pending' => 'En attente',
    'status_confirmed' => 'Confirmée',
    'status_invoiced' => 'Facturée',

    // Flash / exception messages
    'created-from-bon-commande' => 'Commande :reference créée à partir du bon de commande.',
    'facture-generated' => 'Facture :reference générée à partir de la commande.',
    'cannot-convert-cancelled' => 'Un bon de commande annulé ne peut pas être transformé en commande.',
    'only-confirmed-convertible' => 'Seul un bon de commande confirmé peut être transformé en commande.',
    'already-converted' => 'Le bon de commande :reference a déjà été transformé en commande.',
    'only-pending-confirmable' => 'Seule une commande en attente peut être confirmée.',
    'not-invoiceable' => 'Cette commande ne peut pas être facturée dans son statut actuel.',
    'already-invoiced' => 'La commande :reference a déjà été facturée.',
    'confirmed' => 'Commande confirmée.',
    'deleted' => 'Commande supprimée.',
];
