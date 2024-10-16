<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CardController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\VenueController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\BillingController;

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
Route::get('/run', function(){
    return Artisan::call('migrate:fresh --force --seed');
});
Route::get('/', function () {
    return redirect('login');
});

Auth::routes();

// admin urls
Route::middleware(['auth'])->group(function() {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // only admin accessible
    Route::middleware(['admin'])->group(function() {
        // packages
        Route::resource('packages', PackageController::class)->except(['show']);

        // memberships
        Route::resource('memberships', MembershipController::class);
    });
    // only active members and admin accessible
    Route::middleware(['membership'])->group(function() {
        // cards
        Route::resource('cards', CardController::class);
        Route::post('/cards/download', [CardController::class, 'download'])->name('cards.download');

        // venues
        Route::resource('venues', VenueController::class)->parameters(['venues' => 'venue:slug']);
        Route::get('/attach-card/{id}/{card}', [VenueController::class, 'attach_card'])->name('attach_card');
        // vouchers
        Route::resource('vouchers', VoucherController::class);

        // users
        Route::resource('users', App\Http\Controllers\UserController::class);
        Route::get('media/user/{filename}', [App\Http\Controllers\UserController::class, 'image'])
        ->name('users.image');

        // reports
        Route::get('/reports/tap', [ReportController::class, 'tap_report'])->name('reports.tap_report');
        Route::get('/reports/review', [ReportController::class, 'review_report'])->name('reports.review_report');
        Route::get('/reports/google', [ReportController::class, 'google_report'])->name('reports.google_report');

        // reports filter
        Route::get('/tap-filter', [ReportController::class, 'tap_filter'])->name('reports.tap_filter');
        Route::get('/review-filter', [ReportController::class, 'review_filter'])->name('reports.review_filter');
        Route::get('/google-filter', [ReportController::class, 'google_filter'])->name('reports.google_filter');
    });
    
    Route::get('/memberships/subscribe/{id}', [MembershipController::class, 'subscribe'])->name('memberships.subscribe');
    Route::get('/packages/buy', [PackageController::class, 'buy_package'])->name('packages.buy_package');

    Route::get('billing/subscription/{venue}', [BillingController::class, 'showSubscriptionPage'])->name('billing.subscription');
    Route::post('billing/subscribe/{venue}', [BillingController::class, 'createOrUpdateSubscription'])->name('billing.subscribe');
    Route::post('billing/checkout/{venue}', [BillingController::class, 'checkout'])->name('billing.checkout');
    Route::get('billing/success/{venue}', [BillingController::class, 'subscriptionSuccess'])->name('billing.success');
    Route::get('billing/cancel/{venue}', [BillingController::class, 'subscriptionCancel'])->name('billing.cancel');
});

// guest urls
Route::name('guest.')->group(function(){
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
    Route::get('media/logo/{filename}', [VenueController::class, 'image'])
        ->name('venues.image');
});

