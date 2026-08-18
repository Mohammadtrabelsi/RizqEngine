<?php

return [
    // Nouns
    'commandes' => 'Orders',
    'commande' => 'Order',
    'facture' => 'Invoice',
    'reference' => 'Reference',
    'customer' => 'Customer',
    'date' => 'Date',
    'total' => 'Total',
    'note' => 'Note',
    'details' => 'Details',

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
    'confirm' => 'Confirm',
    'delete' => 'Delete',
    'generate-facture' => 'Generate invoice',
    'none-found' => 'No orders found',

    // Statuses
    'status_label' => 'Status',
    'status_pending' => 'Pending',
    'status_confirmed' => 'Confirmed',
    'status_invoiced' => 'Invoiced',

    // Flash / exception messages
    'created-from-bon-commande' => 'Order :reference created from the purchase order.',
    'facture-generated' => 'Invoice :reference generated from the order.',
    'cannot-convert-cancelled' => 'A cancelled purchase order cannot be converted to an order.',
    'only-confirmed-convertible' => 'Only a confirmed purchase order can be converted to an order.',
    'already-converted' => 'Purchase order :reference has already been converted to an order.',
    'only-pending-confirmable' => 'Only a pending order can be confirmed.',
    'not-invoiceable' => 'This order cannot be invoiced in its current status.',
    'already-invoiced' => 'Order :reference has already been invoiced.',
    'confirmed' => 'Order confirmed.',
    'deleted' => 'Order deleted.',
];
