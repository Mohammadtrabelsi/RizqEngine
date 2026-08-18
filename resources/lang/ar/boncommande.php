<?php

return [
    // Nouns
    'bon_commandes' => 'أوامر الشراء',
    'bon_commande' => 'أمر شراء',
    'devis' => 'عرض سعر',
    'reference' => 'المرجع',
    'customer' => 'العميل',
    'date' => 'التاريخ',
    'total' => 'الإجمالي',
    'note' => 'ملاحظة',
    'details' => 'التفاصيل',
    'document_chain' => 'سلسلة المستندات',

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
    'edit' => 'تعديل',
    'edit_bon_commande' => 'تعديل أمر الشراء',
    'update' => 'تحديث',
    'confirm' => 'تأكيد',
    'cancel' => 'إلغاء',
    'delete' => 'حذف',
    'convert-to-commande' => 'تحويل إلى طلب',
    'transform-to-bon-commande' => 'تحويل إلى أمر شراء',
    'view-bon-commande' => 'عرض أمر الشراء',
    'none-found' => 'لا توجد أوامر شراء',

    // Statuses
    'status_label' => 'الحالة',
    'status_draft' => 'مسودة',
    'status_confirmed' => 'مؤكد',
    'status_converted' => 'محوّل',
    'status_cancelled' => 'ملغى',

    // Flash / exception messages
    'created-from-quotation' => 'تم إنشاء أمر الشراء :reference من عرض السعر.',
    'already-converted' => 'تم بالفعل تحويل عرض السعر :reference إلى أمر شراء.',
    'cannot-confirm-cancelled' => 'لا يمكن تأكيد أمر شراء ملغى.',
    'only-draft-confirmable' => 'يمكن تأكيد أمر الشراء في حالة المسودة فقط.',
    'cannot-cancel-converted' => 'لا يمكن إلغاء أمر شراء تم تحويله بالفعل إلى طلب.',
    'only-draft-editable' => 'يمكن تعديل أمر الشراء في حالة المسودة فقط.',
    'updated' => 'تم تحديث أمر الشراء.',
    'confirmed' => 'تم تأكيد أمر الشراء.',
    'cancelled' => 'تم إلغاء أمر الشراء.',
    'deleted' => 'تم حذف أمر الشراء.',
];
