<?php
return [
    // Footer
    'footer_tagline'   => 'خدمات عالية الجودة، مختارة بعناية لتناسب أسلوب حياتك.',
    'footer_email_placeholder' => 'بريدك...',
    'footer_subscribe' => 'انضمام',
    'footer_copyright' => '© :year جلجام. جميع الحقوق محفوظة.',

    // Navbar
    'shop'      => 'المعرض',
    'wishlist'  => 'المفضلة',
    'cart'      => 'الحجز',
    'orders'    => 'حجوزاتي',
    'account'   => 'حسابي',
    'login'     => 'دخول',
    'register'  => 'تسجيل',
    'logout'    => 'تسجيل الخروج',
    'search_placeholder' => 'ابحث في المتجر...',
    'search_mobile_placeholder' => 'ابحث عن خدمة...',
    'view_all'  => 'عرض الكل',
    'account_info' => 'بيانات الحساب',
    'no_phone'  => 'لا يوجد رقم',
    'all_products' => 'جميع الخدمات',
    'create_account' => 'إنشاء حساب',

    'app.book_now' => 'احجز الآن',

    'book_now' => 'احجز الآن',

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
    'products_count'    => ':count خدمة',
    'search_for'        => 'لـ ":term"',
    'view_all_results'  => 'عرض جميع النتائج',
    'no_results'        => 'لا توجد نتائج لـ ":term"',
    'no_products'       => 'لا توجد خدمات',
    'show_all'          => 'عرض الكل',
    'all_products_divider' => 'جميع الخدمات',
    'out_of_stock'      => 'نفد المخزون',
    'featured_badge'    => 'مميز ⭐',
    'sale_badge'        => 'تخفيض',
    'variants_count'    => ':count خيارات',
    'store_breadcrumb'  => 'المتجر',
    'view_all_arrow'    => 'عرض الكل ←',
    'share_copied'      => 'تم نسخ الرابط ✓',
    'share_prompt'      => 'انسخ رابط الخدمة:',
    'share_text'        => ':name — احجز الآن',
    'share_title'       => 'مشاركة',
    'add_to_wishlist'   => 'إضافة للمفضلة',
    'remove_from_wishlist' => 'إزالة من المفضلة',

    'all_categories' => 'الكل',

    'order_success' => [
        'page_title'        => 'تم تأكيد حجزك',
        'heading'           => 'تم تأكيد حجزك! 🎉',
        'subheading'        => 'شكراً لك! سنبدأ بتجهيز حجزك فوراً وسنتواصل معك عند الشحن.',
        'order_number'      => 'رقم الحجز',

        // Invoice card
        'invoice_details'   => 'تفاصيل الحجز',
        'subtotal'          => 'المجموع الفرعي',
        'delivery_fee'      => 'رسوم التوصيل',
        'free'              => 'مجاني 🎉',
        'estimated_delivery'=> 'وقت التوصيل المقدر',
        'working_days'      => 'أيام عمل',
        'grand_total'       => 'الإجمالي الكلي',

        // Shipping address card
        'shipping_address'  => 'عنوان التوصيل',

        // Timeline
        'order_stages'      => 'مراحل الحجز',
        'stage_confirmed'   => 'تم تأكيد الحجز',
        'stage_processing'  => 'جاري التجهيز',
        'stage_processing_sub' => 'يتم تجهيز حجزك الآن',
        'stage_shipping'    => 'بالطريق إليك',
        'stage_shipping_to' => 'إلى',
        'stage_shipping_days'=> '— خلال :days أيام',
        'stage_delivered'   => 'تم التسليم',
        'stage_delivered_sub'=> 'الدفع نقداً عند الاستلام',

        // Actions
        'continue_shopping' => 'متابعة التصفح',
        'my_orders'         => 'حجوزاتي',
    ],

    // ─── Cart Page (cart/index.blade.php) ────────────────────────────────
    'cart' => [
        'page_title'        => 'الحجوزات',
        'heading'           => 'الحجوزات',
        'items_count'       => 'قطعة',
        'continue_shopping' => 'متابعة التصفح',

        // Empty state
        'empty_heading'     => 'الحجز فارغ',
        'empty_sub'         => 'لم تضف أي خدمات بعد. استعرض متجرنا واختر ما يعجبك.',
        'browse_products'   => 'تصفح الخدمات',

        // Table headers
        'col_product'       => 'الخدمة',
        'col_quantity'      => 'الكمية',
        'col_total'         => 'الإجمالي',

        // Per-item
        'per_piece'         => '/ قطعة',
        'remove_title'      => 'إزالة',

        // Summary sidebar
        'order_summary'     => 'ملخص الحجز',
        'subtotal'          => 'المجموع الفرعي',
        'grand_total'       => 'الإجمالي',
        'delivery_note'     => 'رسوم التوصيل تُحسب عند إتمام الحجز بناءً على منطقتك',
        'checkout_btn'      => 'إتمام الحجز بأمان',
        'secure_transactions'=> 'معاملات آمنة ومشفرة',

        // JS confirm
        'confirm_remove'    => 'إزالة هذه الخدمة من الحجز؟',
        'error_update'      => 'حدث خطأ، حاول مجدداً',
        'error_remove'      => 'تعذّر الحذف، حاول مجدداً',
    ],

    // ─── Checkout Page (cart/checkout.blade.php) ──────────────────────────
    'checkout' => [
        'page_title'        => 'إتمام الحجز',
        'heading'           => 'إتمام الحجز',
        'prices_in'         => 'الأسعار بـ :currency (:symbol)',

        // Breadcrumb
        'breadcrumb_store'  => 'المتجر',
        'breadcrumb_cart'   => 'الحجز',
        'breadcrumb_checkout'=> 'إتمام الحجز',

        // Guest banner
        'guest_title'       => 'أنت تحجز كزائر',
        'guest_sub'         => 'يمكنك إتمام الحجز دون تسجيل حساب. أو سجّل دخولك للاستفادة من تتبع حجوزاتك.',
        'login'             => 'تسجيل الدخول',

        // Step 1
        'step1_title'       => 'الخدمات المطلوبة',
        'edit'              => 'تعديل',

        // Step 2
        'step2_title'       => 'بيانات الشحن والتوصيل',
        'field_name'        => 'الاسم الكامل',
        'field_name_ph'     => 'محمد أحمد',
        'field_phone'       => 'رقم الهاتف',
        'field_email'       => 'البريد الإلكتروني',
        'field_email_hint'  => 'لاستقبال تأكيد الحجز',
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
        'order_summary'     => 'ملخص الحجز',
        'subtotal'          => 'المجموع الفرعي',
        'delivery_fee'      => 'رسوم التوصيل',
        'select_zone'       => 'اختر المنطقة',
        'grand_total'       => 'الإجمالي',
        'free'              => 'مجاني 🎉',
        'place_order'       => 'تأكيد الحجز',
        'placing_order'     => 'جارٍ تأكيد الحجز...',
        'btn_hint'          => 'اختر منطقة التوصيل لتفعيل الزر',
        'secure_payment'    => 'دفع آمن ومشفر',
        'cod_reminder'      => 'ستدفع نقداً عند استلام طلبك',
        'delivery_to'       => 'التوصيل إلى :zone خلال :days أيام عمل',
    ],

    'profile' => [
        'page_title'     => 'حسابي',
        'my_orders'      => 'حجوزاتي',
        'wishlist'       => 'المفضلة',
        'logout'         => 'خروج',
        'personal_info'  => 'المعلومات الشخصية',
        'name'           => 'الاسم',
        'phone'          => 'الهاتف',
        'save_changes'   => 'حفظ التعديلات',
        'latest_orders'  => 'آخر الحجوزات',
        'order_no'       => 'حجز رقم :id',
        'no_orders'      => 'لا توجد حجوزات سابقة',
    ],

    'orders' => [
        'page_title' => 'حجوزاتي — المتجر',
        'heading' => 'سجل الحجوزات',
        'continue_shopping' => 'مواصلة التصفح',
        'no_previous_orders' => 'لا توجد حجوزات سابقة حالياً',
        'order_no' => 'حجز رقم :id',
    ],

    'auth' => [
        'login' => [
            'page_title'        => 'تسجيل الدخول',
            'logo_subtitle'     => 'أنشئ حسابك وابدأ الحجز الآن',
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

    'cart_empty'                    => 'الحجز فارغ.',
    'added_to_cart'                 => 'تمت الإضافة إلى الحجز ✓',

    // ── Auth / Access ─────────────────────────────────────────────────────────
    'login_required_checkout'       => 'يرجى تسجيل الدخول لإتمام الحجز.',

    // ── Product / Variant Errors ──────────────────────────────────────────────
    'select_attributes_first'       => 'يرجى اختيار :attributes أولاً',
    'invalid_variant'               => 'الخيار المحدد غير صالح أو غير متاح',
    'select_all_attributes'         => 'يرجى اختيار جميع الخصائص المطلوبة',
    'variant_out_of_stock'          => 'نفد هذا الخيار من المخزون',
    'variant_insufficient_stock'    => 'الكمية غير متوفرة — المتاح: :available قطعة',
    'product_out_of_stock'          => 'الخدمة غير متوفرة حالياً',
    'product_insufficient_stock'    => 'الكمية المطلوبة غير متوفرة — المتاح: :available قطعة',
    'insufficient_stock_for_product'=> 'المخزون غير كافٍ للخدمة: :product',

    // ── Orders ────────────────────────────────────────────────────────────────
    'order_placed_successfully'     => 'تم إرسال حجزك بنجاح!',
    'order_confirmed_successfully'  => 'تم تأكيد حجزك بنجاح!',
    'order_not_found'               => 'الحجز غير موجود.',

    // ── Session ───────────────────────────────────────────────────────────────
    'session_expired_reorder'       => 'انتهت الجلسة. يرجى إعادة الحجز.',
    'session_expired'               => 'انتهت الجلسة.',

    // ── Validation Messages ───────────────────────────────────────────────────
    'validation_full_name_required' => 'الاسم الكامل مطلوب.',
    'validation_phone_required'     => 'رقم الهاتف مطلوب لتأكيد الحجز.',
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
    'add_to_cart'               => 'احجز الآن',
    'adding_to_cart'            => 'جاري الحجز...',
    'product_unavailable'       => 'هذه الخدمة غير متوفرة حالياً',
    'option_unavailable'        => 'هذا الخيار غير متوفر حالياً',
    'cart_success'              => 'تمت إضافة الحجز بنجاح',
    'error_title'               => 'عذراً',
    'warning_title'             => 'تنبيه',
    'server_error'              => 'حدث خطأ في السيرفر',
    'product_sku'               => 'كود الخدمة: :sku',
    'variant_sku_prefix'        => 'كود: ',
    'you_may_also_like'         => 'قد يعجبك أيضاً',
    'stock_in_js'               => 'متوفر',
    'stock_out_js'              => 'نفد المخزون',

    'cancel' => 'إلغاء',

    // ─── Countries ────────────────────────────────────────────────────────────
    'countries' => [
        'page_title'        => 'الدول',
        'manage_title'      => 'إدارة الدول',
        'manage_sub'        => 'حدد الدول المدعومة للشحن مع مناطقها وأسعارها.',

        'add_country'       => 'إضافة دولة',
        'add_first_country' => 'إضافة أول دولة',
        'edit'              => 'تعديل',
        'save'              => 'حفظ الدولة',
        'zones_btn'         => 'المناطق',

        'col_country'       => 'الدولة',
        'col_code'          => 'الرمز',
        'col_zones'         => 'المناطق',
        'col_sort'          => 'الترتيب',
        'col_status'        => 'الحالة',
        'col_actions'       => 'العمليات',

        'status_active'     => 'نشطة',
        'status_inactive'   => 'معطلة',

        'system'                    => 'محمي',
        'cannot_delete_system'      => 'هذه الدولة محمية من النظام ولا يمكن حذفها.',
        'cannot_edit_system_fields' => 'الحقول التالية مقفلة للدول المحمية ولا يمكن تعديلها: :fields.',

        'confirm_delete'    => "حذف دولة ':name' وكل مناطقها؟",

        'created'           => 'تمت إضافة الدولة ":name" بنجاح.',
        'updated'           => 'تم تحديث الدولة بنجاح.',
        'deleted'           => 'تم حذف الدولة.',

        'empty_title'       => 'لا توجد دول مضافة بعد',
        'empty_sub'         => 'ابدأ بإضافة الدول التي تشحن إليها.',

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

        'calling_code_required' => 'رمز الاتصال الدولي مطلوب.',
        'calling_code_regex'    => 'رمز الاتصال يجب أن يحتوي على أرقام فقط (مثال: 962 أو +962).',
    ],

    // ─── Home Sections ────────────────────────────────────────────────────────
    'home_sections' => [
        'create_title'  => 'إضافة قسم جديد',
        'edit_title'    => 'تعديل القسم',
        'back'          => 'العودة للأقسام',
        'new_section'   => 'قسم جديد',

        'type_featured'   => 'الخدمات المميزة',
        'type_latest'     => 'أحدث الخدمات',
        'type_price_high' => 'السعر: من الأعلى للأقل',
        'type_price_low'  => 'السعر: من الأقل للأعلى',
        'type_category'   => 'تصنيف محدد',

        'title_ar'        => 'عنوان القسم (عربي)',
        'title_en'        => 'عنوان القسم (إنجليزي)',
        'type'            => 'نوع القسم',
        'category'        => 'التصنيف',
        'select_category' => 'اختر التصنيف...',
        'limit'           => 'عدد الخدمات',
        'sort_order'      => 'ترتيب العرض',
        'activate'        => 'تفعيل القسم',
        'activate_hint'   => 'يظهر على الصفحة الرئيسية',
        'save'            => 'حفظ القسم',

        'created'   => 'تمت إضافة القسم بنجاح.',
        'updated'   => 'تم تحديث القسم بنجاح.',
        'deleted'   => 'تم حذف القسم.',
    ],

    'wishlist_page' => [
        'title' => 'المفضلة',
        'saved_items' => ':count منتجات محفوظة',
        'saved_items_single' => ':count منتج محفوظ',
        'continue_shopping' => 'متابعة التصفح',
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
    'register_subtitle'         => 'أنشئ حسابك وابدأ الحجز الآن',
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

    'contact_title' => 'اتصل بنا',
    'contact_subtitle' => 'أرسل لنا ملاحظاتك أو شكواك أو أي مشكلة تواجهك.',
    'contact_name' => 'الاسم',
    'contact_email' => 'البريد الإلكتروني',
    'contact_phone' => 'رقم الهاتف',
    'contact_subject' => 'الموضوع',
    'contact_message' => 'الرسالة',
    'contact_send' => 'إرسال',
    'contact_us' => 'اتصل بنا',

    'auth.password_required'    => 'كلمة المرور مطلوبة.',
    'auth.email_required'       => 'البريد الإلكتروني مطلوب.',
    'auth.email_invalid'        => 'البريد الإلكتروني غير صحيح.',
    'auth.email_unique'         => 'البريد الإلكتروني مستخدم مسبقاً.',
    'auth.phone_required'       => 'رقم الهاتف مطلوب.',
    'auth.phone_invalid'        => 'رقم الهاتف غير صحيح.',
    'auth.phone_unique'         => 'رقم الهاتف مستخدم مسبقاً.',
    'auth.too_many_attempts'    => 'محاولات كثيرة. انتظر :seconds ثانية.',
    'auth.failed'               => 'بيانات الدخول غير صحيحة.',
    'auth.identity_missing'     => 'يرجى إدخال رقم الهاتف أو البريد الإلكتروني.',
    'auth.name_required'        => 'الاسم مطلوب.',
    'auth.password_min'         => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل.',
    'auth.password_confirmed'   => 'تأكيد كلمة المرور غير مطابق.',
    'auth.register_success'     => 'تم إنشاء الحساب وتسجيل الدخول بنجاح.',
    'auth.logout_admin'         => 'تم تسجيل خروج المسؤول.',
    'auth.logout_user'          => 'تم تسجيل الخروج.',

    'auth.password_reset_success' => 'تم إعادة تعيين كلمة المرور بنجاح. يمكنك تسجيل الدخول الآن.',

    'auth.forgot_password_title' => 'نسيت كلمة المرور',
    'auth.forgot_password_subtitle' => 'أدخل بريدك الإلكتروني لإعادة تعيين كلمة المرور',
    'auth.forgot_password_heading' => 'نسيت كلمة المرور؟',
    'auth.forgot_password_desc' => 'سنرسل لك رابط إعادة التعيين على بريدك الإلكتروني.',
    'auth.send_reset_link' => 'إرسال رابط إعادة التعيين',
    'auth.back_to_login' => 'العودة إلى تسجيل الدخول',

    // صفحات التسجيل والدخول
    'register_title' => 'إنشاء حساب جديد',
    'register_subtitle' => 'انضم إلينا للحصول على أفضل خدمات غسيل السيارات',
    'register_hint' => 'يرجى إدخال بياناتك لإنشاء الحساب',
    'register_name' => 'الاسم الكامل',
    'register_name_placeholder' => 'أدخل اسمك الثلاثي',
    'register_email_label' => 'البريد الإلكتروني',
    'register_email_otp_hint' => 'سنرسل رمز تحقق إلى بريدك الإلكتروني',
    'register_password' => 'كلمة المرور',
    'register_password_confirm' => 'تأكيد كلمة المرور',
    'register_terms_prefix' => 'بإنشائك للحساب، أنت توافق على',
    'register_terms_link' => 'الشروط والأحكام',
    'register_terms_suffix' => 'الخاصة بنا',
    'register_submit' => 'إنشاء الحساب',
    'register_otp_email' => 'سيتم إرسال كود التحقق فوراً',
    'register_have_account' => 'لديك حساب بالفعل؟',
    'login' => 'تسجيل الدخول',
    'or' => 'أو',
    'back_to_store' => 'العودة للرئيسية',

    // قوة كلمة المرور (JavaScript)
    'pw_strength_prefix' => 'قوة كلمة المرور: ',
    'pw_very_weak' => 'ضعيفة جداً',
    'pw_weak' => 'ضعيفة',
    'pw_medium' => 'متوسطة',
    'pw_strong' => 'قوية جداً',
    'passwords_match' => 'كلمات المرور متطابقة',
    'passwords_no_match' => 'كلمات المرور غير متطابقة',

    // صفحة إعادة تعيين كلمة المرور
    'reset_title' => 'إعادة تعيين كلمة المرور',
    'reset_subtitle' => 'أدخل كلمة المرور الجديدة',
    'reset_hint' => 'اختر كلمة مرور قوية وآمنة لحسابك.',
    'reset_new_password' => 'كلمة المرور الجديدة',
    'reset_submit' => 'تحديث كلمة المرور',
    'back_to_login' => 'العودة إلى تسجيل الدخول',
    'checkout.field_phone_ph'              => 'أدخل رقم الهاتف',
    'checkout.phone_select_country_first'  => 'اختر الدولة أولاً لتحديد المقدمة',
    'checkout.phone_hint_prefix'           => 'الرقم يبدأ بـ',
    'checkout.placing_order_text'          => 'جارٍ تأكيد الحجز...',
    'validation_phone_invalid_format'      => 'رقم الهاتف غير صالح للدولة المختارة',
];