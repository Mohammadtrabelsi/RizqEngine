<?php

return [
    // Nouns
    'bon_commandes' => 'Purchase Orders',
    'bon_commande' => 'Purchase Order',
    'devis' => 'Quotation',
    'reference' => 'Reference',
    'customer' => 'Customer',
    'date' => 'Date',
    'total' => 'Total',
    'note' => 'Note',
    'details' => 'Details',
    'document_chain' => 'Document chain',

    // Info blocks
    'company_info' => 'Company info',
    'customer_info' => 'Customer info',
    'order_info' => 'Order info',
    'email' => 'Email',
    'phone' => 'Phone',
    'net_unit_price' => 'Net unit price',
    'quantity' => 'Quantity',
    'discount' => 'Discount',
    'tax' => 'Tax',
    'sub_total' => 'Sub total',
    'shipping' => 'Shipping',
    'grand_total' => 'Grand total',

    // Actions
    'edit' => 'Edit',
    'edit_bon_commande' => 'Edit purchase order',
    'update' => 'Update',
    'confirm' => 'Confirm',
    'cancel' => 'Cancel',
    'delete' => 'Delete',
    'convert-to-commande' => 'Transform into order',
    'transform-to-bon-commande' => 'Transform into purchase order',
    'view-bon-commande' => 'View purchase order',
    'none-found' => 'No purchase orders found',

    // Statuses
    'status_label' => 'Status',
    'status_draft' => 'Draft',
    'status_confirmed' => 'Confirmed',
    'status_converted' => 'Converted',
    'status_cancelled' => 'Cancelled',

    // Flash / exception messages
    'created-from-quotation' => 'Purchase order :reference created from the quotation.',
    'already-converted' => 'Quotation :reference has already been converted to a purchase order.',
    'cannot-confirm-cancelled' => 'A cancelled purchase order cannot be confirmed.',
    'only-draft-confirmable' => 'Only a draft purchase order can be confirmed.',
    'cannot-cancel-converted' => 'A purchase order already converted to an order cannot be cancelled.',
    'only-draft-editable' => 'Only a draft purchase order can be edited.',
    'updated' => 'Purchase order updated.',
    'confirmed' => 'Purchase order confirmed.',
    'cancelled' => 'Purchase order cancelled.',
    'deleted' => 'Purchase order deleted.',
];
