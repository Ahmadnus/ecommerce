<?php
return [
    // Footer
    'footer_tagline'   => 'منتجات عالية الجودة، مختارة بعناية لتناسب أسلوب حياتك.',
    'footer_email_placeholder' => 'بريدك...',
    'footer_subscribe' => 'انضمام',
    'footer_copyright' => '© :year جلجام. جميع الحقوق محفوظة.',

    // Navbar
    'shop'      => 'المتجر',
    'wishlist'  => 'المفضلة',
    'cart'      => 'السلة',
    'orders'    => 'طلباتي',
    'account'   => 'حسابي',
    'login'     => 'دخول',
    'register'  => 'تسجيل',
    'logout'    => 'تسجيل الخروج',
    'search_placeholder' => 'ابحث في المتجر...',
    'search_mobile_placeholder' => 'ابحث عن منتج...',
    'view_all'  => 'عرض الكل',
    'account_info' => 'بيانات الحساب',
    'no_phone'  => 'لا يوجد رقم',
    'all_products' => 'جميع المنتجات',
    'create_account' => 'إنشاء حساب',





    // Products index
'sort_by'           => 'ترتيب حسب',
'sort_featured'     => 'المميزة أولاً',
'sort_price_asc'    => 'السعر: من الأقل',
'sort_price_desc'   => 'السعر: من الأعلى',
'sort_newest'       => 'الأحدث أولاً',
'sort_featured_short'   => 'المميزة',
'sort_price_asc_short'  => 'السعر ↑',
'sort_price_desc_short' => 'السعر ↓',
'sort_newest_short'     => 'الأحدث',
'sort_btn'          => 'ترتيب',
'search_placeholder_short' => 'بحث...',
'products_count'    => ':count منتج',
'search_for'        => 'لـ ":term"',
'view_all_results'  => 'عرض جميع النتائج',
'no_results'        => 'لا توجد نتائج لـ ":term"',
'no_products'       => 'لا توجد منتجات',
'show_all'          => 'عرض الكل',
'all_products_divider' => 'جميع المنتجات',
'out_of_stock'      => 'نفد المخزون',
'featured_badge'    => 'مميز ⭐',
'sale_badge'        => 'تخفيض',
'variants_count'    => ':count خيارات',
'store_breadcrumb'  => 'المتجر',
'view_all_arrow'    => 'عرض الكل ←',
'share_copied'      => 'تم نسخ الرابط ✓',
'share_prompt'      => 'انسخ رابط المنتج:',
'share_text'        => ':name — تسوق الآن',
'share_title'       => 'مشاركة',
'add_to_wishlist'   => 'إضافة للمفضلة',
'remove_from_wishlist' => 'إزالة من المفضلة',

'all_categories' => 'الكل',
 'order_success' => [
        'page_title'        => 'تم تأكيد طلبك',
        'heading'           => 'تم تأكيد طلبك! 🎉',
        'subheading'        => 'شكراً لك! سنبدأ بتجهيز طلبك فوراً وسنتواصل معك عند الشحن.',
        'order_number'      => 'رقم الطلب',

        // Invoice card
        'invoice_details'   => 'تفاصيل الفاتورة',
        'subtotal'          => 'المجموع الفرعي',
        'delivery_fee'      => 'رسوم التوصيل',
        'free'              => 'مجاني 🎉',
        'estimated_delivery'=> 'وقت التوصيل المقدر',
        'working_days'      => 'أيام عمل',
        'grand_total'       => 'الإجمالي الكلي',

        // Shipping address card
        'shipping_address'  => 'عنوان التوصيل',

        // Timeline
        'order_stages'      => 'مراحل الطلب',
        'stage_confirmed'   => 'تم تأكيد الطلب',
        'stage_processing'  => 'جاري التجهيز',
        'stage_processing_sub' => 'يتم تجهيز طلبك الآن',
        'stage_shipping'    => 'بالطريق إليك',
        'stage_shipping_to' => 'إلى',
        'stage_shipping_days'=> '— خلال :days أيام',
        'stage_delivered'   => 'تم التسليم',
        'stage_delivered_sub'=> 'الدفع نقداً عند الاستلام',

        // Actions
        'continue_shopping' => 'متابعة التسوق',
        'my_orders'         => 'طلباتي',
    ],

    // ─── Cart Page (cart/index.blade.php) ────────────────────────────────
    'cart' => [
        'page_title'        => 'سلة التسوق',
        'heading'           => 'سلة التسوق',
        'items_count'       => 'قطعة',
        'continue_shopping' => 'متابعة التسوق',

        // Empty state
        'empty_heading'     => 'السلة فارغة',
        'empty_sub'         => 'لم تضف أي منتجات بعد. استعرض متجرنا واختر ما يعجبك.',
        'browse_products'   => 'تصفح المنتجات',

        // Table headers
        'col_product'       => 'المنتج',
        'col_quantity'      => 'الكمية',
        'col_total'         => 'الإجمالي',

        // Per-item
        'per_piece'         => '/ قطعة',
        'remove_title'      => 'إزالة',

        // Summary sidebar
        'order_summary'     => 'ملخص الطلب',
        'subtotal'          => 'المجموع الفرعي',
        'grand_total'       => 'الإجمالي',
        'delivery_note'     => 'رسوم التوصيل تُحسب عند إتمام الطلب بناءً على منطقتك',
        'checkout_btn'      => 'إتمام الطلب بأمان',
        'secure_transactions'=> 'معاملات آمنة ومشفرة',

        // JS confirm
        'confirm_remove'    => 'إزالة هذا المنتج من السلة؟',
        'error_update'      => 'حدث خطأ، حاول مجدداً',
        'error_remove'      => 'تعذّر الحذف، حاول مجدداً',
    ],

    // ─── Checkout Page (cart/checkout.blade.php) ──────────────────────────
    'checkout' => [
        'page_title'        => 'إتمام الطلب',
        'heading'           => 'إتمام الطلب',
        'prices_in'         => 'الأسعار بـ :currency (:symbol)',

        // Breadcrumb
        'breadcrumb_store'  => 'المتجر',
        'breadcrumb_cart'   => 'السلة',
        'breadcrumb_checkout'=> 'إتمام الطلب',

        // Guest banner
        'guest_title'       => 'أنت تتسوق كزائر',
        'guest_sub'         => 'يمكنك إتمام طلبك دون تسجيل حساب. أو سجّل دخولك للاستفادة من تتبع طلباتك.',
        'login'             => 'تسجيل الدخول',

        // Step 1
        'step1_title'       => 'المنتجات المطلوبة',
        'edit'              => 'تعديل',

        // Step 2
        'step2_title'       => 'بيانات الشحن والتوصيل',
        'field_name'        => 'الاسم الكامل',
        'field_name_ph'     => 'محمد أحمد',
        'field_phone'       => 'رقم الهاتف',
        'field_email'       => 'البريد الإلكتروني',
        'field_email_hint'  => 'لاستقبال تأكيد الطلب',
        'field_address'     => 'العنوان التفصيلي',
        'field_address_ph'  => 'الشارع، الحي، رقم البناء...',
        'field_city'        => 'المدينة',
        'field_city_ph'     => 'عمّان',
        'field_zip'         => 'الرمز البريدي',
        'field_optional'    => 'اختياري',
        'field_required_mark'=> '*',
        'delivery_zone'     => 'منطقة التوصيل',
        'field_country'     => 'الدولة',
        'country_placeholder'=> 'اختر الدولة...',
        'field_zone'        => 'المنطقة / المدينة',
        'zones_loading'     => 'جاري تحميل المناطق...',
        'zones_unavailable' => 'لا توجد مناطق توصيل متاحة لهذه الدولة حالياً.',
        'zones_error'       => 'تعذّر تحميل المناطق. يرجى المحاولة مجدداً.',
        'field_notes'       => 'ملاحظات',
        'field_notes_ph'    => 'تعليمات خاصة للتوصيل...',

        // Step 3
        'step3_title'       => 'طريقة الدفع',
        'cod_title'         => 'الدفع عند الاستلام',
        'cod_sub'           => 'ادفع نقداً عند وصول طلبك',
        'cod_badge'         => 'متاح',

        // Summary sidebar
        'order_summary'     => 'ملخص الطلب',
        'subtotal'          => 'المجموع الفرعي',
        'delivery_fee'      => 'رسوم التوصيل',
        'select_zone'       => 'اختر المنطقة',
        'grand_total'       => 'الإجمالي',
        'free'              => 'مجاني 🎉',
        'place_order'       => 'تأكيد الطلب',
        'placing_order'     => 'جارٍ تأكيد الطلب...',
        'btn_hint'          => 'اختر منطقة التوصيل لتفعيل الزر',
        'secure_payment'    => 'دفع آمن ومشفر',
        'cod_reminder'      => 'ستدفع نقداً عند استلام طلبك',
        'delivery_to'       => 'التوصيل إلى :zone خلال :days أيام عمل',
    ],

    'profile' => [
    'page_title'     => 'حسابي',
    'my_orders'      => 'طلباتي',
    'wishlist'       => 'المفضلة',
    'logout'         => 'خروج',
    'personal_info'  => 'المعلومات الشخصية',
    'name'           => 'الاسم',
    'phone'          => 'الهاتف',
    'save_changes'   => 'حفظ التعديلات',
    'latest_orders'  => 'آخر الطلبات',
    'order_no'       => 'طلب رقم :id',
    'no_orders'      => 'لا توجد طلبات سابقة',
],

'orders' => [
    'page_title' => 'طلباتي — المتجر',
    'heading' => 'سجل الطلبات',
    'continue_shopping' => 'مواصلة التسوق',
    'no_previous_orders' => 'لا توجد طلبات سابقة حالياً',
    'order_no' => 'طلب رقم :id',
],
'auth' => [
    'login' => [
        'page_title'        => 'تسجيل الدخول',
        'logo_subtitle'     => 'أنشئ حسابك وابدأ التسوق الآن',
        'heading'           => 'تسجيل الدخول',
        'subheading'        => 'مرحباً بك مجدداً، اختر طريقة الدخول',
        'method_phone'      => 'رقم الهاتف',
        'method_email'      => 'البريد الإلكتروني',
        'phone_label'       => 'رقم الهاتف',
        'email_label'       => 'البريد الإلكتروني',
        'email_placeholder' => 'example@mail.com',
        'password_label'    => 'كلمة المرور',
        'password_placeholder' => '••••••••',
        'forgot_password'   => 'نسيت الكلمة؟',
        'remember_me'       => 'تذكرني على هذا الجهاز',
        'submit'            => 'تسجيل الدخول',
        'or'                => 'أو',
        'no_account'        => 'ليس لديك حساب؟',
        'register'          => 'أنشئ حساباً جديداً',
    ],
],

 'cart_empty'                    => 'سلة التسوق فارغة.',
    'added_to_cart'                 => 'تمت الإضافة إلى السلة ✓',

    // ── Auth / Access ─────────────────────────────────────────────────────────
    'login_required_checkout'       => 'يرجى تسجيل الدخول لإتمام عملية الشراء.',

    // ── Product / Variant Errors ──────────────────────────────────────────────
    'select_attributes_first'       => 'يرجى اختيار :attributes أولاً',
    'invalid_variant'               => 'المتغير المحدد غير صالح أو غير متاح',
    'select_all_attributes'         => 'يرجى اختيار جميع الخصائص المطلوبة',
    'variant_out_of_stock'          => 'نفد هذا الخيار من المخزون',
    'variant_insufficient_stock'    => 'الكمية غير متوفرة — المتاح: :available قطعة',
    'product_out_of_stock'          => 'المنتج غير متوفر حالياً',
    'product_insufficient_stock'    => 'الكمية المطلوبة غير متوفرة — المتاح: :available قطعة',
    'insufficient_stock_for_product'=> 'المخزون غير كافٍ للمنتج: :product',

    // ── Orders ────────────────────────────────────────────────────────────────
    'order_placed_successfully'     => 'تم إرسال طلبك بنجاح!',
    'order_confirmed_successfully'  => 'تم تأكيد طلبك بنجاح!',
    'order_not_found'               => 'الطلب غير موجود.',

    // ── Session ───────────────────────────────────────────────────────────────
    'session_expired_reorder'       => 'انتهت الجلسة. يرجى إعادة الطلب.',
    'session_expired'               => 'انتهت الجلسة.',

    // ── Validation Messages ───────────────────────────────────────────────────
    'validation_full_name_required' => 'الاسم الكامل مطلوب.',
    'validation_phone_required'     => 'رقم الهاتف مطلوب لتوصيل الطلب.',
    'validation_address_required'   => 'العنوان التفصيلي مطلوب.',
    'validation_city_required'      => 'المدينة مطلوبة.',
    'validation_country_required'   => 'الدولة مطلوبة.',
    'validation_zone_required'      => 'منطقة التوصيل مطلوبة.',
    // Product show page
'shop_breadcrumb'           => 'المتجر',
'discount_badge'            => ':percent% خصم',
'featured_badge_full'       => '⭐ مميز',
'sale_label'                => 'تخفيض',
'savings'                   => 'وفّر :amount :symbol',
'in_stock'                  => 'متوفر (:qty قطعة)',
'out_of_stock_full'         => 'نفد المخزون',
'attr_required'             => 'يرجى اختيار :attr',
'cart_error_missing'        => 'يرجى اختيار جميع الخصائص المطلوبة',
'cart_error_missing_count'  => 'يرجى اختيار جميع الخصائص المطلوبة — :count خصائص ناقصة',
'add_to_cart'               => 'أضف إلى السلة',
'adding_to_cart'            => 'جاري الإضافة...',
'product_unavailable'       => 'هذا المنتج غير متوفر حالياً',
'option_unavailable'        => 'هذا الخيار غير متوفر حالياً',
'cart_success'              => 'تمت إضافة المنتج للسلة بنجاح',
'error_title'               => 'عذراً',
'warning_title'             => 'تنبيه',
'server_error'              => 'حدث خطأ في السيرفر',
'product_sku'               => 'كود المنتج: :sku',
'variant_sku_prefix'        => 'كود: ',
'you_may_also_like'         => 'قد يعجبك أيضاً',
'stock_in_js'               => 'متوفر',
'stock_out_js'              => 'نفد المخزون',


    'cancel' => 'إلغاء',

    // ─── Countries ────────────────────────────────────────────────────────────
    'countries' => [

        // Page titles & headings
        'page_title'        => 'الدول',
        'manage_title'      => 'إدارة الدول',
        'manage_sub'        => 'حدد الدول المدعومة للشحن مع مناطقها وأسعارها.',

        // Buttons & actions
        'add_country'       => 'إضافة دولة',
        'add_first_country' => 'إضافة أول دولة',
        'edit'              => 'تعديل',
        'save'              => 'حفظ الدولة',
        'zones_btn'         => 'المناطق',

        // Table headers
        'col_country'       => 'الدولة',
        'col_code'          => 'الرمز',
        'col_zones'         => 'المناطق',
        'col_sort'          => 'الترتيب',
        'col_status'        => 'الحالة',
        'col_actions'       => 'العمليات',

        // Status badges
        'status_active'     => 'نشطة',
        'status_inactive'   => 'معطلة',

        // System badge & protection messages
        'system'                    => 'محمي',
        'cannot_delete_system'      => 'هذه الدولة محمية من النظام ولا يمكن حذفها.',
        'cannot_edit_system_fields' => 'الحقول التالية مقفلة للدول المحمية ولا يمكن تعديلها: :fields.',

        // Confirm dialog
        'confirm_delete'    => "حذف دولة ':name' وكل مناطقها؟",

        // Flash messages
        'created'           => 'تمت إضافة الدولة ":name" بنجاح.',
        'updated'           => 'تم تحديث الدولة بنجاح.',
        'deleted'           => 'تم حذف الدولة.',

        // Empty state
        'empty_title'       => 'لا توجد دول مضافة بعد',
        'empty_sub'         => 'ابدأ بإضافة الدول التي تشحن إليها.',

        // Form fields
        'field_name_ar'     => 'اسم الدولة (عربي)',
        'field_name_en'     => 'اسم الدولة (إنجليزي)',
        'field_code'        => 'الرمز الدولي',
        'field_code_hint'   => 'مثل: JO, SY, SA',
        'field_calling_code'=> 'رمز الاتصال',
        'field_calling_hint'=> 'أرقام فقط، مثل: 962 أو +962',
        'field_sort_order'  => 'ترتيب العرض',
        'field_currencies'  => 'العملات المدعومة',
        'field_default_currency' => 'العملة الافتراضية',
        'field_active'      => 'تفعيل الدولة',
        'field_active_hint' => 'تظهر في خيارات الشحن عند إتمام الطلب',

        // Validation messages
        'calling_code_required' => 'رمز الاتصال الدولي مطلوب.',
        'calling_code_regex'    => 'رمز الاتصال يجب أن يحتوي على أرقام فقط (مثال: 962 أو +962).',
    ],

    // ─── Home Sections ────────────────────────────────────────────────────────
    'home_sections' => [

        // Page titles
        'create_title'  => 'إضافة قسم جديد',
        'edit_title'    => 'تعديل القسم',
        'back'          => 'العودة للأقسام',
        'new_section'   => 'قسم جديد',

        // Type labels (used in model typeLabels() and dropdowns)
        'type_featured'   => 'المنتجات المميزة',
        'type_latest'     => 'أحدث المنتجات',
        'type_price_high' => 'السعر: من الأعلى للأقل',
        'type_price_low'  => 'السعر: من الأقل للأعلى',
        'type_category'   => 'تصنيف محدد',

        // Form fields
        'title_ar'        => 'عنوان القسم (عربي)',
        'title_en'        => 'عنوان القسم (إنجليزي)',
        'type'            => 'نوع القسم',
        'category'        => 'التصنيف',
        'select_category' => 'اختر التصنيف...',
        'limit'           => 'عدد المنتجات',
        'sort_order'      => 'ترتيب العرض',
        'activate'        => 'تفعيل القسم',
        'activate_hint'   => 'يظهر على الصفحة الرئيسية',
        'save'            => 'حفظ القسم',

        // Flash messages
        'created'   => 'تمت إضافة القسم بنجاح.',
        'updated'   => 'تم تحديث القسم بنجاح.',
        'deleted'   => 'تم حذف القسم.',
    ],
'wishlist_page' => [
    'title' => 'المفضلة',
    'saved_items' => ':count منتجات محفوظة',
    'saved_items_single' => ':count منتج محفوظ',
    'continue_shopping' => 'متابعة التسوق',
    'empty_title' => 'قائمة المفضلة فارغة',
    'empty_sub' => 'لم تضف أي منتجات إلى المفضلة بعد. استعرض المتجر واضغط على القلب ❤️ لأي منتج يعجبك.',
    'browse_products' => 'تصفح المنتجات',
    'discount' => 'خصم :percent%',
    'featured' => 'مميز',
    'out_of_stock' => 'نفد المخزون',
    'add' => 'أضف',
    'options_count' => ':count خيارات متاحة',
],

'wishlist_messages' => [
    'removed' => 'تمت الإزالة من المفضلة',
    'added'   => 'تمت الإضافة إلى المفضلة ❤️',
],
// ── Register page ──────────────────────────────────────────
'register_title'            => 'إنشاء حساب جديد',
'register_subtitle'         => 'أنشئ حسابك وابدأ التسوق الآن',
'register_hint'             => 'أدخل بياناتك وسيصلك رمز التحقق',
'register_method'           => 'طريقة التسجيل',
'register_phone'            => 'رقم الهاتف',
'register_email_tab'        => 'البريد الإلكتروني',
'register_name'             => 'الاسم الكامل',
'register_name_placeholder' => 'محمد أحمد',
'register_phone_label'      => 'رقم الهاتف',
'register_email_label'      => 'البريد الإلكتروني',
'register_email_otp_hint'   => 'سيُرسل رمز التحقق إلى بريدك الإلكتروني',
'register_password'         => 'كلمة المرور',
'register_password_confirm' => 'تأكيد كلمة المرور',
'register_terms_prefix'     => 'بإنشاء الحساب، أنت توافق على',
'register_terms_link'       => 'شروط الاستخدام',
'register_terms_suffix'     => 'وسياسة الخصوصية',
'register_submit'           => 'إنشاء الحساب',
'register_otp_email'        => '— سيصلك رمز على بريدك',
'register_otp_phone'        => '— سيصلك رمز على هاتفك',
'register_have_account'     => 'لديك حساب بالفعل؟',
'or'                        => 'أو',
'back_to_store'             => 'العودة إلى المتجر',

// ── Password strength ──────────────────────────────────────
'pw_strength_prefix'        => 'قوة كلمة المرور: ',
'pw_very_weak'              => 'ضعيفة جداً',
'pw_weak'                   => 'ضعيفة',
'pw_medium'                 => 'متوسطة',
'pw_strong'                 => 'قوية',
'passwords_match'           => 'كلمتا المرور متطابقتان ✓',
'passwords_no_match'        => 'كلمتا المرور غير متطابقتين',
];
