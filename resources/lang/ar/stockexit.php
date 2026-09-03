<?php

return [
    'stock_exits' => 'أذون الصرف',
    'bon_de_sortie' => 'إذن صرف',
    'bon_dentree' => 'إذن إدخال',
    'add_exit' => 'إذن صرف جديد',
    'create_exit' => 'إنشاء إذن صرف',
    'exit_details' => 'تفاصيل إذن الصرف',
    'entry_details' => 'تفاصيل إذن الإدخال',
    'no_exits_found' => 'لا يوجد إذن صرف',
    'no_products_selected' => 'لم يتم اختيار أي منتج',
    'out_of_stock' => 'نفد المخزون: لا يمكن إخراج هذا المنتج.',

    'reference' => 'المرجع',
    'auto_generated' => 'يُنشأ تلقائياً',
    'date' => 'التاريخ',
    'reason' => 'السبب',
    'destination' => 'الوجهة',
    'responsible' => 'المسؤول',
    'note' => 'ملاحظة',
    'products' => 'المنتجات',

    'reason_loan' => 'إعارة',
    'reason_jobsite' => 'ورشة',
    'reason_subcontracting' => 'مناولة',
    'reason_transfer' => 'تحويل',

    'driver' => 'السائق',
    'vehicle' => 'المركبة',
    'select_driver' => '— اختر سائقًا —',
    'select_vehicle' => '— اختر مركبة —',

    'status_in_transit' => 'قيد النقل / مصروف',
    'status_closed' => 'مُغلق',

    'quantity_out' => 'الكمية المصروفة',
    'quantity_returned' => 'الكمية المُرجعة',
    'quantity_received' => 'الكمية المستلمة',
    'quantity_lost' => 'الكمية المستهلكة/المفقودة',
    'quantity_outstanding' => 'المتبقّي للإرجاع',

    'declare_return' => 'تسجيل الإرجاع',
    'confirm_return' => 'تأكيد الإرجاع',
    'reception_date' => 'تاريخ الاستلام',
    'origin_reference' => 'مرجع إذن الصرف الأصلي',
    'linked_entries' => 'أذون الإدخال المرتبطة',
    'exit_already_closed' => 'إذن الصرف هذا مُغلق بالفعل.',
    'return_control_hint' => 'الرقابة الفعلية إلزامية: أدخل الكمية المستلمة فعلياً (المُعاد إدخالها للمخزون) وعند الحاجة الكمية المراد شطبها كاستهلاك/فقد. يبقى الباقي «قيد النقل» ويمكن إرجاعه لاحقاً. يُغلق الإذن تلقائياً بمجرد تسوية جميع البنود.',

    'line_status' => 'الحالة',
    'full_return' => 'إرجاع كامل',
    'partial_return' => 'إرجاع جزئي',
    'fully_consumed' => 'مستهلك/مفقود',

    'exit-created' => 'تم إنشاء إذن الصرف بنجاح',
    'exit-deleted' => 'تم حذف إذن الصرف بنجاح',
    'entry-created' => 'تم تسجيل إذن الإدخال',
    'entry-created-closed' => 'تم تسجيل إذن الإدخال وإغلاق إذن الصرف',

    // البيع بالأمانة / الإيداع
    'kind' => 'نوع الصرف',
    'kind_standard' => 'صرف عادي (إعارة / ورشة / تحويل)',
    'kind_consignment' => 'صرف للإيداع (بيع بالأمانة / بضاعة غير مباعة)',
    'consignee' => 'المودَع لديه (العميل)',
    'select_consignee' => '— اختر المودَع لديه —',
    'quantity_sold' => 'الكمية المباعة',
    'declare_consignment_return' => 'تسجيل إرجاع البضاعة غير المباعة',
    'consignment_return_hint' => 'أدخل كمية البضاعة غير المباعة المُرجَعة فعلياً. يُعتبر الفرق (الكمية الصادرة − الكمية المُرجَعة) مباعاً ويُفوتر تلقائياً على المودَع لديه.',
    'generated_invoice' => 'فاتورة التسوية',
    'consignment-regularised' => 'تم تسجيل الإرجاع وإعادة إدخال غير المباع وفوترة المبيعات',
];
