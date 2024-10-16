<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CardController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\VenueController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\SettingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

use Illuminate\Support\Facades\Artisan;
Route::get('/clear', function () {
    return Artisan::call('optimize:clear');
});
Route::get('/run', function () {
    return Artisan::call('migrate:fresh --force --seed');
});
Route::get('/', function () {
    return redirect('login');
});

Auth::routes();

// admin urls
Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // only admin accessible
    Route::middleware(['admin'])->group(function () {
        // packages
        Route::resource('packages', PackageController::class)->except(['show']);

        // subscriptions
        Route::resource('subscriptions', SubscriptionController::class);

        Route::get('/settings', [SettingController::class, 'show'])->name('settings.show');
        Route::get('/settings/edit', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    });
    // cards
    Route::resource('cards', CardController::class);
    Route::post('/cards/download', [CardController::class, 'download'])->name('cards.download');

    // venues
    Route::resource('venues', VenueController::class)->parameters(['venues' => 'venue:slug']);
    Route::get('/venues/attach-card/{id}/{card}', [VenueController::class, 'attach_card'])->name('attach_card');
    Route::get('logo/{filename}', [VenueController::class, 'image'])
        ->name('venues.image');
    // vouchers
    Route::resource('vouchers', VoucherController::class);

    // users
    Route::resource('users', App\Http\Controllers\UserController::class);
    Route::get('avatar/{filename}', [App\Http\Controllers\UserController::class, 'image'])
        ->name('users.image');

    // reports
    Route::get('/reports/tap', [ReportController::class, 'tap_report'])->name('reports.tap_report')->middleware(['standard', 'premium']);
    Route::get('/reports/review', [ReportController::class, 'review_report'])->name('reports.review_report')->middleware('premium');
    Route::get('/reports/google', [ReportController::class, 'google_report'])->name('reports.google_report')->middleware('premium');

    // reports filter
    Route::get('/tap-filter', [ReportController::class, 'tap_filter'])->name('reports.tap_filter');
    Route::get('/review-filter', [ReportController::class, 'review_filter'])->name('reports.review_filter');
    Route::get('/google-filter', [ReportController::class, 'google_filter'])->name('reports.google_filter');

    // Route::get('/subscriptions/subscribe/{venue}', [SubscriptionController::class, 'subscribe'])->name('subscriptions.subscribe');
    // Route::post('billing/subscribe/{venue}', [SubscriptionController::class, 'subscribe'])->name('billing.subscribe');
    Route::get('/packages/buy', [PackageController::class, 'buy_package'])->name('packages.buy_package');


    // Create a subscription for a venue
    Route::post('/billing/subscription', [BillingController::class, 'createSubscription'])->name('billing.createSubscription');

    // Add a card to an active subscription
    Route::post('/billing/add-card', [BillingController::class, 'addCardToSubscription'])->name('billing.addCardToSubscription');

    // Handle successful payment redirection (with venue_id as a parameter)
    Route::get('/billing/success/{venue_id}', [BillingController::class, 'paymentSuccess'])->name('billing.success');

    // Handle failed payment redirection (with venue_id as a parameter)
    Route::get('/billing/failed/{venue_id}', [BillingController::class, 'paymentFailed'])->name('billing.failed');

    // Webhook to handle updates from Xendit (payment success, subscription status)
    Route::post('/billing/webhook', [BillingController::class, 'handleWebhook'])->name('billing.webhook');

});

// guest urls
Route::name('guest.')->group(function () {
    Route::get('/card/{card}', [GuestController::class, 'qr_card'])->name('qr_card');
    Route::get('/card-check/{card}', [GuestController::class, 'check_card'])->name('check_card');
    Route::get('/{venue}/{card}', [GuestController::class, 'view_card'])->name('view_card');

    // guest voucher urls
    Route::get('/{venue}/voucher/{uuid}', [GuestController::class, 'qr_voucher'])->name('qr_voucher');
    Route::get('/{venue}/voucher/{card}/{type}', [GuestController::class, 'create_voucher'])->name('create_voucher');
    Route::post('/{venue}/voucher/{card}', [GuestController::class, 'store_voucher'])->name('store_voucher');
    Route::get('/{venue}/claim/{uuid}', [GuestController::class, 'view_voucher'])->name('view_voucher');
    Route::post('/{venue}/claim/{uuid}', [GuestController::class, 'claim_voucher'])->name('claim_voucher');

    // guest review urls
    Route::get('/{venue}/review/{card}', [GuestController::class, 'create_review'])->name('create_review');
    Route::post('/{venue}/review/{card}', [GuestController::class, 'store_review'])->name('store_review');
});