<?php

return [
    // Nouns
    'commandes' => 'الطلبات',
    'commande' => 'طلب',
    'facture' => 'فاتورة',
    'reference' => 'المرجع',
    'customer' => 'العميل',
    'date' => 'التاريخ',
    'total' => 'الإجمالي',
    'note' => 'ملاحظة',
    'details' => 'التفاصيل',

    // Info blocks
    'company_info' => 'معلومات الشركة',
    'customer_info' => 'معلومات العميل',
    'order_info' => 'معلومات الطلب',
    'email' => 'البريد الإلكتروني',
    'phone' => 'الهاتف',
    'net_unit_price' => 'سعر الوحدة الصافي',
    'quantity' => 'الكمية',
    'discount' => 'الخصم',
    'tax' => 'الضريبة',
    'sub_total' => 'المجموع الفرعي',
    'shipping' => 'الشحن',
    'grand_total' => 'الإجمالي الكلي',

    // Actions
    'confirm' => 'تأكيد',
    'delete' => 'حذف',
    'generate-facture' => 'إنشاء الفاتورة',
    'none-found' => 'لا توجد طلبات',

    // Statuses
    'status_label' => 'الحالة',
    'status_pending' => 'قيد الانتظار',
    'status_confirmed' => 'مؤكد',
    'status_invoiced' => 'مفوتر',

    // Flash / exception messages
    'created-from-bon-commande' => 'تم إنشاء الطلب :reference من أمر الشراء.',
    'facture-generated' => 'تم إنشاء الفاتورة :reference من الطلب.',
    'cannot-convert-cancelled' => 'لا يمكن تحويل أمر شراء ملغى إلى طلب.',
    'only-confirmed-convertible' => 'يمكن تحويل أمر الشراء المؤكد فقط إلى طلب.',
    'already-converted' => 'تم بالفعل تحويل أمر الشراء :reference إلى طلب.',
    'only-pending-confirmable' => 'يمكن تأكيد الطلب قيد الانتظار فقط.',
    'not-invoiceable' => 'لا يمكن فوترة هذا الطلب في حالته الحالية.',
    'already-invoiced' => 'تم بالفعل فوترة الطلب :reference.',
    'confirmed' => 'تم تأكيد الطلب.',
    'deleted' => 'تم حذف الطلب.',
];
