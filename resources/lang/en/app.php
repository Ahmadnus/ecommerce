<?php
return [
    // Footer
    'footer_tagline'   => 'High-quality products, carefully curated to match your lifestyle.',
    'footer_email_placeholder' => 'Your email...',
    'footer_subscribe' => 'Subscribe',
    'footer_copyright' => '© :year Gilgam. All rights reserved.',

    // Navbar
    'shop'      => 'Shop',
    'wishlist'  => 'Wishlist',
    'cart'      => 'Cart',
    'orders'    => 'My Orders',
    'account'   => 'My Account',
    'login'     => 'Login',
    'register'  => 'Register',
    'logout'    => 'Logout',
    'search_placeholder' => 'Search the store...',
    'search_mobile_placeholder' => 'Search for a product...',
    'view_all'  => 'View all',
    'account_info' => 'Account info',
    'no_phone'  => 'No phone number',
    'all_products' => 'All products',
    'create_account' => 'Register',

    // Products index
'sort_by'           => 'Sort by',
'sort_featured'     => 'Featured first',
'sort_price_asc'    => 'Price: low to high',
'sort_price_desc'   => 'Price: high to low',
'sort_newest'       => 'Newest first',
'sort_featured_short'   => 'Featured',
'sort_price_asc_short'  => 'Price ↑',
'sort_price_desc_short' => 'Price ↓',
'sort_newest_short'     => 'Newest',
'sort_btn'          => 'Sort',
'search_placeholder_short' => 'Search...',
'products_count'    => ':count products',
'search_for'        => 'for ":term"',
'view_all_results'  => 'View all results',
'no_results'        => 'No results for ":term"',
'no_products'       => 'No products found',
'show_all'          => 'View all',
'all_products_divider' => 'All products',
'out_of_stock'      => 'Out of stock',
'featured_badge'    => 'Featured ⭐',
'sale_badge'        => 'Sale',
'variants_count'    => ':count options',
'store_breadcrumb'  => 'Shop',
'view_all_arrow'    => 'View all →',
'share_copied'      => 'Link copied ✓',
'share_prompt'      => 'Copy product link:',
'share_text'        => ':name — Shop now',
'share_title'       => 'Share',
'add_to_wishlist'   => 'Add to wishlist',
'remove_from_wishlist' => 'Remove from wishlist',

'all_categories' => 'All',



 'order_success' => [
        'page_title'        => 'Order Confirmed',
        'heading'           => 'Your order has been confirmed! 🎉',
        'subheading'        => 'Thank you! We will start preparing your order immediately and contact you when it ships.',
        'order_number'      => 'Order Number',

        // Invoice card
        'invoice_details'   => 'Invoice Details',
        'subtotal'          => 'Subtotal',
        'delivery_fee'      => 'Delivery Fee',
        'free'              => 'Free 🎉',
        'estimated_delivery'=> 'Estimated Delivery Time',
        'working_days'      => 'business days',
        'grand_total'       => 'Grand Total',

        // Shipping address card
        'shipping_address'  => 'Shipping Address',

        // Timeline
        'order_stages'      => 'Order Stages',
        'stage_confirmed'   => 'Order Confirmed',
        'stage_processing'  => 'Processing',
        'stage_processing_sub' => 'Your order is being prepared now',
        'stage_shipping'    => 'On Its Way',
        'stage_shipping_to' => 'To',
        'stage_shipping_days'=> '— within :days days',
        'stage_delivered'   => 'Delivered',
        'stage_delivered_sub'=> 'Cash on delivery',

        // Actions
        'continue_shopping' => 'Continue Shopping',
        'my_orders'         => 'My Orders',
    ],

    // ─── Cart Page (cart/index.blade.php) ────────────────────────────────
    'cart' => [
        'page_title'        => 'Shopping Cart',
        'heading'           => 'Shopping Cart',
        'items_count'       => 'item(s)',
        'continue_shopping' => 'Continue Shopping',

        // Empty state
        'empty_heading'     => 'Your cart is empty',
        'empty_sub'         => 'You haven\'t added any products yet. Browse our store and find something you like.',
        'browse_products'   => 'Browse Products',

        // Table headers
        'col_product'       => 'Product',
        'col_quantity'      => 'Quantity',
        'col_total'         => 'Total',

        // Per-item
        'per_piece'         => '/ piece',
        'remove_title'      => 'Remove',

        // Summary sidebar
        'order_summary'     => 'Order Summary',
        'subtotal'          => 'Subtotal',
        'grand_total'       => 'Total',
        'delivery_note'     => 'Shipping fees are calculated at checkout based on your region',
        'checkout_btn'      => 'Secure Checkout',
        'secure_transactions'=> 'Safe & encrypted transactions',

        // JS confirm
        'confirm_remove'    => 'Remove this product from the cart?',
        'error_update'      => 'An error occurred, please try again',
        'error_remove'      => 'Could not remove, please try again',
    ],

    // ─── Checkout Page (cart/checkout.blade.php) ──────────────────────────
    'checkout' => [
        'page_title'        => 'Checkout',
        'heading'           => 'Checkout',
        'prices_in'         => 'Prices in :currency (:symbol)',

        // Breadcrumb
        'breadcrumb_store'  => 'Store',
        'breadcrumb_cart'   => 'Cart',
        'breadcrumb_checkout'=> 'Checkout',

        // Guest banner
        'guest_title'       => 'You are shopping as a guest',
        'guest_sub'         => 'You can complete your order without creating an account. Or log in to track your orders.',
        'login'             => 'Log In',

        // Step 1
        'step1_title'       => 'Ordered Products',
        'edit'              => 'Edit',

        // Step 2
        'step2_title'       => 'Shipping & Delivery Details',
        'field_name'        => 'Full Name',
        'field_name_ph'     => 'John Smith',
        'field_phone'       => 'Phone Number',
        'field_email'       => 'Email Address',
        'field_email_hint'  => 'To receive order confirmation',
        'field_address'     => 'Detailed Address',
        'field_address_ph'  => 'Street, district, building number...',
        'field_city'        => 'City',
        'field_city_ph'     => 'New York',
        'field_zip'         => 'Postal Code',
        'field_optional'    => 'Optional',
        'field_required_mark'=> '*',
        'delivery_zone'     => 'Delivery Zone',
        'field_country'     => 'Country',
        'country_placeholder'=> 'Select country...',
        'field_zone'        => 'Region / City',
        'zones_loading'     => 'Loading regions...',
        'zones_unavailable' => 'No delivery zones available for this country at the moment.',
        'zones_error'       => 'Failed to load regions. Please try again.',
        'field_notes'       => 'Notes',
        'field_notes_ph'    => 'Special delivery instructions...',

        // Step 3
        'step3_title'       => 'Payment Method',
        'cod_title'         => 'Cash on Delivery',
        'cod_sub'           => 'Pay in cash when your order arrives',
        'cod_badge'         => 'Available',

        // Summary sidebar
        'order_summary'     => 'Order Summary',
        'subtotal'          => 'Subtotal',
        'delivery_fee'      => 'Delivery Fee',
        'select_zone'       => 'Select Region',
        'grand_total'       => 'Total',
        'free'              => 'Free 🎉',
        'place_order'       => 'Place Order',
        'placing_order'     => 'Placing order...',
        'btn_hint'          => 'Select a delivery zone to enable the button',
        'secure_payment'    => 'Safe & encrypted payment',
        'cod_reminder'      => 'You will pay in cash upon receiving your order',
        'delivery_to'       => 'Delivery to :zone within :days business days',
    ],
    'profile' => [
    'page_title'     => 'My Account',
    'my_orders'      => 'My Orders',
    'wishlist'       => 'Wishlist',
    'logout'         => 'Logout',
    'personal_info'  => 'Personal Information',
    'name'           => 'Name',
    'phone'          => 'Phone',
    'save_changes'   => 'Save Changes',
    'latest_orders'  => 'Latest Orders',
    'order_no'       => 'Order #:id',
    'no_orders'      => 'No previous orders',
],
'orders' => [
    'page_title' => 'My Orders — Store',
    'heading' => 'Order History',
    'continue_shopping' => 'Continue shopping',
    'no_previous_orders' => 'No previous orders yet',
    'order_no' => 'Order #:id',
],
'auth' => [
    'login' => [
        'page_title'        => 'Login',
        'logo_subtitle'     => 'Create your account and start shopping now',
        'heading'           => 'Login',
        'subheading'        => 'Welcome back, choose your login method',
        'method_phone'      => 'Phone number',
        'method_email'      => 'Email',
        'phone_label'       => 'Phone number',
        'email_label'       => 'Email',
        'email_placeholder' => 'example@mail.com',
        'password_label'    => 'Password',
        'password_placeholder' => '••••••••',
        'forgot_password'   => 'Forgot password?',
        'remember_me'       => 'Remember me on this device',
        'submit'            => 'Login',
        'or'                => 'Or',
        'no_account'        => "Don't have an account?",
        'register'          => 'Create a new account',
    ],
],


 // ── Cart ─────────────────────────────────────────────────────────────────
    'cart_empty'                    => 'Your shopping cart is empty.',
    'added_to_cart'                 => 'Added to cart ✓',

    // ── Auth / Access ─────────────────────────────────────────────────────────
    'login_required_checkout'       => 'Please log in to complete your purchase.',

    // ── Product / Variant Errors ──────────────────────────────────────────────
    'select_attributes_first'       => 'Please select :attributes first',
    'invalid_variant'               => 'The selected variant is invalid or unavailable.',
    'select_all_attributes'         => 'Please select all required attributes.',
    'variant_out_of_stock'          => 'This option is out of stock.',
    'variant_insufficient_stock'    => 'Quantity unavailable — available: :available',
    'product_out_of_stock'          => 'This product is currently unavailable.',
    'product_insufficient_stock'    => 'Requested quantity unavailable — available: :available',
    'insufficient_stock_for_product'=> 'Insufficient stock for product: :product',

    // ── Orders ────────────────────────────────────────────────────────────────
    'order_placed_successfully'     => 'Your order has been submitted successfully!',
    'order_confirmed_successfully'  => 'Your order has been confirmed successfully!',
    'order_not_found'               => 'Order not found.',

    // ── Session ───────────────────────────────────────────────────────────────
    'session_expired_reorder'       => 'Session expired. Please place your order again.',
    'session_expired'               => 'Session expired.',

    // ── Validation Messages ───────────────────────────────────────────────────
    'validation_full_name_required' => 'Full name is required.',
    'validation_phone_required'     => 'Phone number is required for delivery.',
    'validation_address_required'   => 'Detailed address is required.',
    'validation_city_required'      => 'City is required.',
    'validation_country_required'   => 'Country is required.',
    'validation_zone_required'      => 'Delivery zone is required.',
    // Product show page
'shop_breadcrumb'           => 'Shop',
'discount_badge'            => ':percent% off',
'featured_badge_full'       => '⭐ Featured',
'sale_label'                => 'Sale',
'savings'                   => 'Save :amount :symbol',
'in_stock'                  => 'In stock (:qty units)',
'out_of_stock_full'         => 'Out of stock',
'attr_required'             => 'Please select :attr',
'cart_error_missing'        => 'Please select all required options',
'cart_error_missing_count'  => 'Please select all required options — :count missing',
'add_to_cart'               => 'Add to cart',
'adding_to_cart'            => 'Adding...',
'product_unavailable'       => 'This product is currently unavailable',
'option_unavailable'        => 'This option is currently unavailable',
'cart_success'              => 'Product added to cart successfully',
'error_title'               => 'Sorry',
'warning_title'             => 'Warning',
'server_error'              => 'A server error occurred',
'product_sku'               => 'SKU: :sku',
'variant_sku_prefix'        => 'SKU: ',
'you_may_also_like'         => 'You may also like',
'stock_in_js'               => 'In stock',
'stock_out_js'              => 'Out of stock',


    'cancel' => 'Cancel',

    // ─── Countries ────────────────────────────────────────────────────────────
    'countries' => [

        // Page titles & headings
        'page_title'        => 'Countries',
        'manage_title'      => 'Manage Countries',
        'manage_sub'        => 'Define the supported shipping countries with their zones and prices.',

        // Buttons & actions
        'add_country'       => 'Add Country',
        'add_first_country' => 'Add First Country',
        'edit'              => 'Edit',
        'save'              => 'Save Country',
        'zones_btn'         => 'Zones',

        // Table headers
        'col_country'       => 'Country',
        'col_code'          => 'Code',
        'col_zones'         => 'Zones',
        'col_sort'          => 'Order',
        'col_status'        => 'Status',
        'col_actions'       => 'Actions',

        // Status badges
        'status_active'     => 'Active',
        'status_inactive'   => 'Inactive',

        // System badge & protection messages
        'system'                    => 'System',
        'cannot_delete_system'      => 'This country is system-protected and cannot be deleted.',
        'cannot_edit_system_fields' => 'The following fields are locked for system countries and cannot be changed: :fields.',

        // Confirm dialog
        'confirm_delete'    => "Delete country ':name' and all its zones?",

        // Flash messages
        'created'           => 'Country ":name" has been added successfully.',
        'updated'           => 'Country updated successfully.',
        'deleted'           => 'Country deleted successfully.',

        // Empty state
        'empty_title'       => 'No countries added yet',
        'empty_sub'         => 'Start by adding the countries you ship to.',

        // Form fields
        'field_name_ar'     => 'Country Name (Arabic)',
        'field_name_en'     => 'Country Name (English)',
        'field_code'        => 'ISO Code',
        'field_code_hint'   => 'e.g. JO, SY, SA',
        'field_calling_code'=> 'Calling Code',
        'field_calling_hint'=> 'Digits only, e.g. 962 or +962',
        'field_sort_order'  => 'Sort Order',
        'field_currencies'  => 'Supported Currencies',
        'field_default_currency' => 'Default Currency',
        'field_active'      => 'Activate Country',
        'field_active_hint' => 'Visible in checkout shipping options',

        // Validation messages
        'calling_code_required' => 'The international calling code is required.',
        'calling_code_regex'    => 'The calling code must contain digits only (e.g. 962 or +962).',
    ],

    // ─── Home Sections ────────────────────────────────────────────────────────
    'home_sections' => [

        // Page titles
        'create_title'  => 'Add New Section',
        'edit_title'    => 'Edit Section',
        'back'          => 'Back to Sections',
        'new_section'   => 'New Section',

        // Type labels (used in model typeLabels() and dropdowns)
        'type_featured'   => 'Featured Products',
        'type_latest'     => 'Latest Products',
        'type_price_high' => 'Price: High to Low',
        'type_price_low'  => 'Price: Low to High',
        'type_category'   => 'Specific Category',

        // Form fields
        'title_ar'        => 'Section Title (Arabic)',
        'title_en'        => 'Section Title (English)',
        'type'            => 'Section Type',
        'category'        => 'Category',
        'select_category' => 'Select category...',
        'limit'           => 'Product Count',
        'sort_order'      => 'Display Order',
        'activate'        => 'Activate Section',
        'activate_hint'   => 'Appears on the homepage',
        'save'            => 'Save Section',

        // Flash messages
        'created'   => 'Section added successfully.',
        'updated'   => 'Section updated successfully.',
        'deleted'   => 'Section deleted.',
    ],
'wishlist_page' => [
    'title' => 'Wishlist',
    'saved_items' => ':count saved items',
    'saved_items_single' => ':count saved item',
    'continue_shopping' => 'Continue shopping',
    'empty_title' => 'Your wishlist is empty',
    'empty_sub' => 'You haven’t added any products yet. Browse the store and tap ❤️ on products you like.',
    'browse_products' => 'Browse products',
    'discount' => ':percent% off',
    'featured' => 'Featured',
    'out_of_stock' => 'Out of stock',
    'add' => 'Add',
    'options_count' => ':count options available',
],
'wishlist_messages' => [
    'removed' => 'Removed from wishlist',
    'added'   => 'Added to wishlist ❤️',
],
];
