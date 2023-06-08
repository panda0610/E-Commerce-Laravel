<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';

Route::get('/login/{lang?}', 'Auth\LoginController@showLoginForm')->name('login');

Route::get('/password/resets/{lang?}', 'Auth\LoginController@showLinkRequestForm')->name('change.langPass');

Route::get(
    '/', [
           'as' => 'dashboard',
           'uses' => 'DashboardController@index',
       ]
)->middleware(
    [
        'XSS',
    ]
);
Route::get(
    '/dashboard', [
                    'as' => 'dashboard',
                    'uses' => 'DashboardController@index',
                ]
)->middleware(
    [
        'XSS',
        'auth',
    ]
);
Route::group(
    [
        'middleware' => [
            'auth',
        ],
    ], function (){
    Route::resource('stores', 'StoreController');
    Route::post('store-setting/{id}', 'StoreController@savestoresetting')->name('settings.store');
}
);
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ], function (){
    Route::get('change-language/{lang}', 'LanguageController@changeLanquage')->name('change.language')->middleware(
        [
            'auth',
            'XSS',
        ]
    );
    Route::get('manage-language/{lang}', 'LanguageController@manageLanguage')->name('manage.language')->middleware(
        [
            'auth',
            'XSS',
        ]
    );
    Route::post('store-language-data/{lang}', 'LanguageController@storeLanguageData')->name('store.language.data')->middleware(
        [
            'auth',
            'XSS',
        ]
    );
    Route::get('create-language', 'LanguageController@createLanguage')->name('create.language')->middleware(
        [
            'auth',
            'XSS',
        ]
    );
    Route::post('store-language', 'LanguageController@storeLanguage')->name('store.language')->middleware(
        [
            'auth',
            'XSS',
        ]
    );

    Route::delete('/lang/{lang}', 'LanguageController@destroyLang')->name('lang.destroy')->middleware(
        [
            'auth',
            'XSS',
        ]
    );
}
);
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ], function (){
    Route::get('store-grid/grid', 'StoreController@grid')->name('store.grid');
    Route::get('store-customDomain/customDomain', 'StoreController@customDomain')->name('store.customDomain');
    Route::get('store-subDomain/subDomain', 'StoreController@subDomain')->name('store.subDomain');
    Route::get('store-plan/{id}/plan', 'StoreController@upgradePlan')->name('plan.upgrade');
    Route::get('store-plan-active/{id}/plan/{pid}', 'StoreController@activePlan')->name('plan.active');
    Route::DELETE('store-delete/{id}', 'StoreController@storedestroy')->name('user.destroy');
    Route::DELETE('ownerstore-delete/{id}', 'StoreController@ownerstoredestroy')->name('ownerstore.destroy');
    Route::get('store-edit/{id}', 'StoreController@storedit')->name('user.edit');;
    Route::Put('store-update/{id}', 'StoreController@storeupdate')->name('user.update');;
}
);

Route::get('/store-change/{id}', 'StoreController@changeCurrantStore')->name('change_store')->middleware(
    [
        'auth',
        'XSS',
    ]
);

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],

    function (){
        Route::get('change-language/{lang}', 'LanguageController@changeLanquage')->name('change.language')->middleware(
            [
                'auth',
                'XSS',
            ]
        );
        Route::get('manage-language/{lang}', 'LanguageController@manageLanguage')->name('manage.language')->middleware(
            [
                'auth',
                'XSS',
            ]
        );
        Route::post('store-language-data/{lang}', 'LanguageController@storeLanguageData')->name('store.language.data')->middleware(
            [
                'auth',
                'XSS',
            ]
        );
        Route::get('create-language', 'LanguageController@createLanguage')->name('create.language')->middleware(
            [
                'auth',
                'XSS',
            ]
        );
        Route::post('store-language', 'LanguageController@storeLanguage')->name('store.language')->middleware(
            [
                'auth',
                'XSS',
            ]
        );

        Route::delete('/lang/{lang}', 'LanguageController@destroyLang')->name('lang.destroy')->middleware(
            [
                'auth',
                'XSS',
            ]
        );
    }
);
Route::get(
    '/change/mode', [
                      'as' => 'change.mode',
                      'uses' => 'DashboardController@changeMode',
                  ]
);

Route::get('profile', 'DashboardController@profile')->name('profile')->middleware(
    [
        'auth',
        'XSS',
    ]
);
Route::put('change-password', 'DashboardController@updatePassword')->name('update.password');
Route::put('edit-profile', 'DashboardController@editprofile')->name('update.account')->middleware(
    [
        'auth',
        'XSS',
    ]
);

Route::get('storeanalytic', 'StoreAnalytic@index')->middleware('auth')->name('storeanalytic')->middleware(['XSS']);

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ], function (){
    Route::post('business-setting', 'SettingController@saveBusinessSettings')->name('business.setting');
    Route::post('company-setting', 'SettingController@saveCompanySettings')->name('company.setting');
    Route::post('email-setting', 'SettingController@saveEmailSettings')->name('email.setting');
    Route::post('system-setting', 'SettingController@saveSystemSettings')->name('system.setting');
    Route::post('pusher-setting', 'SettingController@savePusherSettings')->name('pusher.setting');
    Route::get('test-mail', 'SettingController@testMail')->name('test.mail');
    Route::post('test-mail', 'SettingController@testSendMail')->name('test.send.mail');
    Route::get('settings', 'SettingController@index')->name('settings');
    Route::post('payment-setting', 'SettingController@savePaymentSettings')->name('payment.setting');
    Route::post('owner-payment-setting/{slug?}', 'SettingController@saveOwnerPaymentSettings')->name('owner.payment.setting');
    Route::post('owner-email-setting/{slug?}', 'SettingController@saveOwneremailSettings')->name('owner.email.setting');
    Route::post('owner-twilio-setting/{slug?}', 'SettingController@saveOwnertwilioSettings')->name('owner.twilio.setting');
}
);
Route::resource('product_categorie', 'ProductCategorieController')->middleware(
    [
        'auth',
        'XSS',
    ]
);
Route::resource('product_tax', 'ProductTaxController')->middleware(
    [
        'auth',
        'XSS',
    ]
);
//=================================product import/export=============================
Route::get('shipping/export', 'ShippingController@fileExport')->name('shipping.export');
Route::get('shipping/import/export', 'ShippingController@fileImportExport')->name('shipping.file.import');
Route::post('shipping/import', 'ShippingController@fileImport')->name('shipping.import');

Route::resource('shipping', 'ShippingController')->middleware(
    [
        'auth',
        'XSS',
    ]
);

Route::resource('location', 'LocationController')->middleware(
    [
        'auth',
        'XSS',
    ]
);
Route::resource('custom-page', 'PageOptionController')->middleware(['auth']);
Route::resource('blog', 'BlogController')->middleware(
    [
        'auth'
    ]
);
Route::get('blog-social', 'BlogController@socialBlog')->name('blog.social')->middleware(
    [
        'auth',
        'XSS',
    ]
);
Route::post('store-social-blog', 'BlogController@storeSocialblog')->name('store.socialblog')->middleware(
    [
        'auth',
        'XSS',
    ]
);

Route::resource('shipping', 'ShippingController')->middleware(
    [
        'auth',
        'XSS',
    ]
);


Route::get('/plan/error/{flag}', ['as' => 'error.plan.show','uses' => 'PaymentWallController@planerror']);
Route::get(
    '/plans', [
                'as' => 'plans.index',
                'uses' => 'PlanController@index',
            ]
)->middleware(
    [
        'auth',
        'XSS',
    ]
);
Route::get(
    '/plans/create', [
                       'as' => 'plans.create',
                       'uses' => 'PlanController@create',
                   ]
)->middleware(
    [
        'auth',
        'XSS',
    ]
);
Route::post(
    '/plans', [
                'as' => 'plans.store',
                'uses' => 'PlanController@store',
            ]
)->middleware(
    [
        'auth',
        'XSS',
    ]
);
Route::get(
    '/plans/edit/{id}', [
                          'as' => 'plans.edit',
                          'uses' => 'PlanController@edit',
                      ]
)->middleware(
    [
        'auth',
        'XSS',
    ]
);
Route::put(
    '/plans/{id}', [
                     'as' => 'plans.update',
                     'uses' => 'PlanController@update',
                 ]
)->middleware(
    [
        'auth',
        'XSS',
    ]
);

Route::post(
    '/user-plans/', [
                      'as' => 'update.user.plan',
                      'uses' => 'PlanController@userPlan',
                  ]
)->middleware(
    [
        'auth',
        'XSS',
    ]
);
Route::resource('orders', 'OrderController')->middleware(
    [
        'XSS',
        'auth',
    ]
);

Route::get('order/export', 'OrderController@fileExport')->name('order.export');


Route::get('order-receipt/{id}', 'OrderController@receipt')->name('order.receipt')->middleware('auth');
Route::group(
    [
        'middleware' => [
            'XSS',
        ],
    ], function (){
    Route::resource('rating', 'RattingController');
    Route::post('rating_view', 'RattingController@rating_view')->name('rating.rating_view');
    Route::get('rating/{slug?}/product/{id}', 'RattingController@rating')->name('rating');
    Route::post('stor_rating/{slug?}/product/{id}', 'RattingController@stor_rating')->name('stor_rating');
    Route::post('edit_rating/product/{id}', 'RattingController@edit_rating')->name('edit_rating');
}
);
Route::group(
    [
        'middleware' => [
            'XSS',
        ],
    ], function (){
    Route::resource('subscriptions', 'SubscriptionController');
    Route::POST('subscriptions/{id}', 'SubscriptionController@store_email')->name('subscriptions.store_email');
}
);
Route::group(
    [
        'middleware' => [
            'auth',
        ],
    ], function (){


    Route::get(
        '/product-variants/create', [
                                      'as' => 'product.variants.create',
                                      'uses' => 'ProductController@productVariantsCreate',
                                  ]
    );
    Route::get(
        '/get-product-variants-possibilities', [
                                                 'as' => 'get.product.variants.possibilities',
                                                 'uses' => 'ProductController@getProductVariantsPossibilities',
                                             ]
    );
    Route::get('product/grid', 'ProductController@grid')->name('product.grid');
    Route::delete('product/{id}/delete', 'ProductController@fileDelete')->name('products.file.delete');
    Route::delete('product/variant/{id}/', 'ProductController@VariantDelete')->name('products.variant.delete');
}
);
//=================================product import/export=============================
Route::get('product/export', 'ProductController@fileExport')->name('product.export');
Route::get('product/import/export', 'ProductController@fileImportExport')->name('product.file.import');
Route::post('product/import', 'ProductController@fileImport')->name('product.import');

Route::resource('product', 'ProductController')->middleware(['auth','XSS']);
Route::post('product/{id}/update', 'ProductController@productUpdate')->name('products.update')->middleware('auth');
Route::get(
    'get-products-variant-quantity', [
                                       'as' => 'get.products.variant.quantity',
                                       'uses' => 'ProductController@getProductsVariantQuantity',
                                   ]
);
Route::get(
    '/store-resource/edit/display/{id}', [
                          'as' => 'store-resource.edit.display',
                          'uses' => 'StoreController@storeenable',
                      ]
)->middleware(
    [
        'auth',
        'XSS',
    ]
);
Route::Put(
    '/store-resource/display/{id}', [
                     'as' => 'store-resource.display',
                     'uses' => 'StoreController@storeenableupdate',
                 ]
)->middleware(
    [
        'auth',
        'XSS',
    ]
);

Route::resource('store-resource', 'StoreController')->middleware(
    [
        'auth',
        'XSS',
    ]
);

Route::get('page/{slug?}', 'StoreController@pageOptionSlug')->name('pageoption.slug');
Route::get('store-blog/{slug?}', 'StoreController@StoreBlog')->name('store.blog');
Route::get('store-blog-view/{slug?}/blog/{id}', 'StoreController@StoreBlogView')->name('store.store_blog_view');

Route::get('store/{slug?}', 'StoreController@storeSlug')->name('store.slug');
Route::get('store/{slug?}/categorie/{name?}', 'StoreController@product')->name('store.categorie.product');
Route::get('user-cart-item/{slug?}/cart','StoreController@StoreCart')->name('store.cart');
Route::get('user-address/{slug?}/useraddress', 'StoreController@userAddress')->name('user-address.useraddress');
Route::get('store-payment/{slug?}/userpayment', 'StoreController@userPayment')->name('store-payment.payment');
Route::get('store/{slug?}/product/{id}', 'StoreController@productView')->name('store.product.product_view');
Route::post('user-product_qty/{slug?}/product/{id}/{variant_id?}', 'StoreController@productqty')->name('user-product_qty.product_qty');
Route::post('customer/{slug}', 'StoreController@customer')->name('store.customer');
Route::post('user-location/{slug}/location/{id}', 'StoreController@UserLocation')->name('user.location');
Route::post('user-shipping/{slug}/shipping/{id}', 'StoreController@UserShipping')->name('user.shipping');
Route::post('save-rating/{slug?}', 'StoreController@saverating')->name('store.saverating');
Route::delete('delete_cart_item/{slug?}/product/{id}/{variant_id?}', 'StoreController@delete_cart_item')->name('delete.cart_item');
Route::delete('delete_wishlist_item/{slug?}/product/{id}/', 'StoreController@delete_wishlist_item')->name('delete.wishlist_item');

Route::get('store-complete/{slug?}/{id}', 'StoreController@complete')->name('store-complete.complete');

Route::post('add-to-cart/{slug?}/{id}/{variant_id?}', 'StoreController@addToCart')->name('user.addToCart');

Route::group(
    ['middleware' => ['XSS']], function (){
    Route::get('order', 'StripePaymentController@index')->name('order.index');
    Route::get('/stripe/{code}', 'StripePaymentController@stripe')->name('stripe');
    Route::post('/stripe/{slug?}', 'StripePaymentController@stripePost')->name('stripe.post');
    Route::post('stripe-payment', 'StripePaymentController@addpayment')->name('stripe.payment');
}
);

Route::post('pay-with-paypal/{slug?}', 'PaypalController@PayWithPaypal')->name('pay.with.paypal')->middleware(['XSS']);

Route::get('{id}/get-payment-status{slug?}', 'PaypalController@GetPaymentStatus')->name('get.payment.status')->middleware(['XSS']);
Route::get('{slug?}/customerorder/{id}', 'StoreController@customerorder')->name('customer.order');
Route::get('{slug?}/order/{id}', 'StoreController@userorder')->name('user.order');

Route::post('{slug?}/whatsapp', 'StoreController@whatsapp')->name('user.whatsapp');
Route::post('{slug?}/telegram', 'StoreController@telegram')->name('user.telegram');

Route::post('{slug?}/cod', 'StoreController@cod')->name('user.cod');
Route::post('{slug?}/bank_transfer', 'StoreController@bank_transfer')->name('user.bank_transfer');

Route::get(
    '/apply-coupon', [
                       'as' => 'apply.coupon',
                       'uses' => 'CouponController@applyCoupon',
                   ]
)->middleware(
    [
        'auth',
        'XSS',
    ]
);
Route::get(
    '/apply-productcoupon', [
                              'as' => 'apply.productcoupon',
                              'uses' => 'ProductCouponController@applyProductCoupon',
                          ]
);

Route::get('productcoupon/import/export', 'ProductCouponController@fileImportExport')->name('productcoupon.file.import');
Route::post('productcoupon/import', 'ProductCouponController@fileImport')->name('productcoupon.import');
Route::get('productcoupon/export', 'ProductCouponController@fileExport')->name('productcoupon.export');

Route::resource('coupons', 'CouponController')->middleware(
    [
        'auth',
        'XSS',
    ]
);
Route::post(
    'prepare-payment', [
                         'as' => 'prepare.payment',
                         'uses' => 'PlanController@preparePayment',
                     ]
)->middleware(
    [
        'auth',
        'XSS',
    ]
);
Route::get(
    '/payment/{code}', [
                         'as' => 'payment',
                         'uses' => 'PlanController@payment',
                     ]
)->middleware(
    [
        'auth',
        'XSS',
    ]
);

Route::post('plan-pay-with-paypal', 'PaypalController@planPayWithPaypal')->name('plan.pay.with.paypal')->middleware(
    [
        'auth',
        'XSS',
    ]
);
Route::get('{id}/get-store-payment-status', 'PaypalController@storeGetPaymentStatus')->name('get.store.payment.status')->middleware(
    [
        'auth',
        'XSS',
    ]
);
Route::get(
    'qr-code', function (){
    return QrCode::generate();
}
);

Route::get('change-language-store/{slug?}/{lang}', 'LanguageController@changeLanquageStore')->name('change.languagestore')->middleware(['XSS']);

Route::resource('product-coupon', 'ProductCouponController')->middleware(
    [
        'auth',
        'XSS',
    ]
);

//    Payments Callbacks

Route::get('paystack/{slug}/{code}/{order_id}', 'PaymentController@paystackPayment')->name('paystack');
Route::get('flutterwave/{slug}/{tran_id}/{order_id}', 'PaymentController@flutterwavePayment')->name('flutterwave');
Route::get('razorpay/{slug}/{pay_id}/{order_id}', 'PaymentController@razerpayPayment')->name('razorpay');
Route::post('{slug}/paytm/prepare-payments/', 'PaymentController@paytmOrder')->name('paytm.prepare.payments');
Route::post('paytm/callback/', 'PaymentController@paytmCallback')->name('paytm.callback');
Route::post('{slug}/mollie/prepare-payments/', 'PaymentController@mollieOrder')->name('mollie.prepare.payments');
Route::get('{slug}/{order_id}/mollie/callback/', 'PaymentController@mollieCallback')->name('mollie.callback');
Route::post('{slug}/mercadopago/prepare-payments/', 'PaymentController@mercadopagoPayment')->name('mercadopago.prepare');
Route::any('{slug}/mercadopago/callback/', 'PaymentController@mercadopagoCallback')->name('mercado.callback');

Route::post('{slug}/coingate/prepare-payments/', 'PaymentController@coingatePayment')->name('coingate.prepare');
Route::get('coingate/callback', 'PaymentController@coingateCallback')->name('coingate.callback');

Route::post('{slug}/skrill/prepare-payments/', 'PaymentController@skrillPayment')->name('skrill.prepare.payments');
Route::get('skrill/callback', 'PaymentController@skrillCallback')->name('skrill.callback');


//ORDER PAYMENTWALL
Route::post('{slug}/paymentwall/store-slug/', 'StoreController@paymentwallstoresession')->name('paymentwall.session.store');
Route::any('{slug}/order/error/{flag}', 'PaymentWallController@orderpaymenterror')->name('order.callback.error');
Route::any('/{slug}/paymentwall/order',['as' => 'paymentwall.index','uses'=>'PaymentWallController@orderindex']);
Route::post('/{slug}/order-pay-with-paymentwall/', ['as' => 'order.pay.with.paymentwall', 'uses' => 'PaymentWallController@orderPayWithPaymentwall']);

// Plan Purchase Payments methods

Route::get('plan/prepare-amount', 'PlanController@planPrepareAmount')->name('plan.prepare.amount');
Route::get('paystack-plan/{code}/{plan_id}', 'PaymentController@paystackPlanGetPayment')->name('paystack.plan.callback')->middleware(['auth']);
Route::get('flutterwave-plan/{code}/{plan_id}', 'PaymentController@flutterwavePlanGetPayment')->name('flutterwave.plan.callback')->middleware(['auth']);
Route::get('razorpay-plan/{code}/{plan_id}', 'PaymentController@razorpayPlanGetPayment')->name('razorpay.plan.callback')->middleware(['auth']);
Route::post('mercadopago-prepare-plan', 'PaymentController@mercadopagoPaymentPrepare')->name('mercadopago.prepare.plan')->middleware(['auth']);
Route::any('plan-mercado-callback/{plan_id}', 'PaymentController@mercadopagoPaymentCallback')->name('plan.mercado.callback')->middleware(['auth']);

Route::post('paytm-prepare-plan', 'PaymentController@paytmPaymentPrepare')->name('paytm.prepare.plan')->middleware(['auth']);
Route::post('paytm-payment-plan', 'PaymentController@paytmPlanGetPayment')->name('plan.paytm.callback')->middleware(['auth']);

Route::post('mollie-prepare-plan', 'PaymentController@molliePaymentPrepare')->name('mollie.prepare.plan')->middleware(['auth']);
Route::get('mollie-payment-plan/{slug}/{plan_id}', 'PaymentController@molliePlanGetPayment')->name('plan.mollie.callback')->middleware(['auth']);

Route::post('coingate-prepare-plan', 'PaymentController@coingatePaymentPrepare')->name('coingate.prepare.plan')->middleware(['auth']);
Route::get('coingate-payment-plan', 'PaymentController@coingatePlanGetPayment')->name('coingate.mollie.callback')->middleware(['auth']);

Route::post('skrill-prepare-plan', 'PaymentController@skrillPaymentPrepare')->name('skrill.prepare.plan')->middleware(['auth']);
Route::get('skrill-payment-plan', 'PaymentController@skrillPlanGetPayment')->name('plan.skrill.callback')->middleware(['auth']);
Route::post('store/{slug?}', 'StoreController@changeTheme')->name('store.changetheme');
Route::get('{slug?}/edit-products/{theme?}', 'StoreController@Editproducts')->name('store.editproducts')->middleware(
    [
        'auth',
        'XSS',
    ]
);
//PLAN PAYMENTWALL
Route::post('/planpayment',['as' => 'paymentwall','uses'=>'PaymentWallController@planpay'])->middleware(['auth','XSS']);
Route::post('/paymentwall-payment/{plan}',['as' => 'paymentwall.payment','uses' =>'PaymentWallController@planPayWithPaymentWall'])->middleware(['auth','XSS']);


Route::post('{slug?}/store-edit-products/{theme?}', 'StoreController@StoreEditProduct')->name('store.storeeditproducts')->middleware(['auth']);
Route::delete('{slug?}/{theme}/brand/{id}/delete/', 'StoreController@brandfileDelete')->name('brand.file.delete')->middleware(['auth']);

//================================= Custom Landing Page ====================================//


//================================= Custom Massage Page ====================================//
Route::post('/store/custom-msg/{slug}', 'StoreController@customMassage')->name('customMassage');
Route::post('store/get-massage/{slug}', 'StoreController@getWhatsappUrl')->name('get.whatsappurl');
Route::get('store/remove-session/{slug}', 'StoreController@removeSession')->name('remove.session');

//WISH LIST
Route::get('store/{slug}/wishlist', 'StoreController@Wishlist')->name('store.wishlist');
Route::post('store/{slug}/addtowishlist/{id}', 'StoreController@AddToWishlist')->name('store.addtowishlist');

Route::post('store/{slug}/downloadable_prodcut', 'StoreController@downloadable_prodcut')->name('user.downloadable_prodcut');


// Email Templates
Route::get('email_template_lang/{id}/{lang?}', 'EmailTemplateController@manageEmailLang')->name('manage.email.language')->middleware(['auth']);
Route::put('email_template_store/{pid}', 'EmailTemplateController@storeEmailLang')->name('store.email.language')->middleware(['auth']);
Route::put('email_template_status/{id}', 'EmailTemplateController@updateStatus')->name('status.email.language')->middleware(['auth']);
Route::resource('email_template', 'EmailTemplateController')->middleware(
    [
        'auth',
        'XSS',
    ]
);

//=================================Plan Request Module ====================================//
Route::get('plan_request', 'PlanRequestController@index')->name('plan_request.index')->middleware(['auth','XSS',]);
Route::get('request_frequency/{id}', 'PlanRequestController@requestView')->name('request.view')->middleware(['auth','XSS',]);
Route::get('request_send/{id}', 'PlanRequestController@userRequest')->name('send.request')->middleware(['auth','XSS',]);
Route::get('request_response/{id}/{response}', 'PlanRequestController@acceptRequest')->name('response.request')->middleware(['auth','XSS',]);
Route::get('request_cancel/{id}', 'PlanRequestController@cancelRequest')->name('request.cancel')->middleware(['auth','XSS',]);


/*=================================Customer Login==========================================*/

Route::get('{slug}/user-create', 'StoreController@userCreate')->name('store.usercreate');
Route::post('{slug}/user-create', 'StoreController@userStore')->name('store.userstore');

Route::get('{slug}/customer-login','Customer\Auth\CustomerLoginController@showLoginForm')->name('customer.loginform');
Route::post('{slug}/customer-login/{cart?}','Customer\Auth\CustomerLoginController@login')->name('customer.login');


Route::get('{slug}/customer-home', 'StoreController@customerHome')->name('customer.home')->middleware('customerauth');

Route::get('{slug}/customer-profile/{id}','Customer\Auth\CustomerLoginController@profile')->name('customer.profile')->middleware('customerauth');
Route::put('{slug}/customer-profile/{id}','Customer\Auth\CustomerLoginController@profileUpdate')->name('customer.profile.update')->middleware('customerauth');
Route::put('{slug}/customer-profile-password/{id}','Customer\Auth\CustomerLoginController@updatePassword')->name('customer.profile.password')->middleware('customerauth');
Route::post('{slug}/customer-logout','Customer\Auth\CustomerLoginController@logout')->name('customer.logout');

/*==================================Recaptcha====================================================*/

Route::post('/recaptcha-settings',['as' => 'recaptcha.settings.store','uses' =>'SettingController@recaptchaSettingStore'])->middleware(['auth','XSS']);


Route::get('/remove-coupn','StoreController@remcoup')->name('apply.removecoupn');

Route::any('user-reset-password/{id}', 'StoreController@employeePassword')->name('user.reset');
Route::post('user-reset-password/{id}', 'StoreController@employeePasswordReset')->name('user.password.update');

// ===================================customer view==========================================

Route::get('/customer','StoreController@customerindex')->name('customer.index');
Route::get('/customer/view/{id}', 'StoreController@customershow')->name('customer.show');
