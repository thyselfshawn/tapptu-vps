<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\ReviewStatusEnum;
use App\Enums\CardStatusEnum;
use App\Enums\VenueStatusEnum;
use App\Enums\VoucherStatusEnum;
use App\Enums\ReviewTypeEnum;
use App\Enums\TapTypeEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name')->nullable();
            $table->string('token')->nullable();
            $table->enum('status', CardStatusEnum::values())->default(CardStatusEnum::pending->value);
            $table->timestamps();
        });

        Schema::create('venues', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('logo')->default('logo.png');
            $table->string('pagecolor')->nullable();
            $table->string('fontcolor')->nullable();
            $table->text('voucher')->nullable();
            $table->string('instaurl')->nullable();
            $table->string('googleplaceid')->nullable();
            $table->string('googlereviewstart')->nullable();
            $table->string('stripe_customer_id')->nullable();
            $table->boolean('notification')->default(true);
            $table->enum('status', VenueStatusEnum::values())->default(VenueStatusEnum::pending->value);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('venue_cards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('card_id');
            $table->unsignedBigInteger('venue_id');
            $table->timestamps();

            $table->foreign('card_id')->references('id')->on('cards')->onDelete('cascade');
            $table->foreign('venue_id')->references('id')->on('venues')->onDelete('cascade');
        });

        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->enum('name', ['standard', 'premium']);            
            $table->enum('type', ['monthly', 'yearly']);
            $table->integer('first_price');
            $table->integer('second_price');
            $table->string('stripe_price_id')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
        
        Schema::create('memberships', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('venue_id');
            $table->unsignedBigInteger('package_id');
            $table->boolean('status')->defaul(false);
            $table->string('stripe_subscription_id')->nullable();
            $table->timestamp('end_at');
            $table->timestamps();

            $table->foreign('venue_id')->references('id')->on('venues')->onDelete('cascade');
            $table->foreign('package_id')->references('id')->on('packages')->onDelete('cascade');
        });

        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->unsignedBigInteger('venue_id');
            $table->unsignedBigInteger('card_id');
            $table->enum('type', ReviewTypeEnum::values());
            $table->text('message')->nullable();
            $table->enum('status', ReviewStatusEnum::values())->default(ReviewStatusEnum::pending->value);
            $table->timestamps();

            $table->foreign('card_id')->references('id')->on('cards')->onDelete('cascade');
            $table->foreign('venue_id')->references('id')->on('venues')->onDelete('cascade');
            
        });

        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone');
            $table->uuid('uuid')->unique();
            $table->timestamps();
        });

        Schema::create('contact_reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contact_id');
            $table->unsignedBigInteger('review_id');
            $table->timestamps();

            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
            $table->foreign('review_id')->references('id')->on('reviews')->onDelete('cascade');
        });

        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->enum('status', VoucherStatusEnum::values())->default(VoucherStatusEnum::pending->value);
            $table->text('text');
            $table->timestamp('claimed_at')->nullable();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('contact_id');
            $table->unsignedBigInteger('venue_id');
            $table->unsignedBigInteger('card_id');
            $table->timestamps();

            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
            $table->foreign('venue_id')->references('id')->on('venues')->onDelete('cascade');
            $table->foreign('card_id')->references('id')->on('cards')->onDelete('cascade');
        });

        Schema::create('taps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('card_id');
            $table->unsignedBigInteger('venue_id');
            $table->enum('type', TapTypeEnum::values());
            $table->timestamps();

            $table->foreign('card_id')->references('id')->on('cards')->onDelete('cascade');
            $table->foreign('venue_id')->references('id')->on('venues')->onDelete('cascade');
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('wa_number');
            $table->string('wa_instanceid');
            $table->string('wa_accesstoken');
            $table->string('stripe_publishable');
            $table->string('stripe_secret');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cards');
        Schema::dropIfExists('venues');
        Schema::dropIfExists('venue_cards');
        Schema::dropIfExists('packages');
        Schema::dropIfExists('memberships');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('contacts');
        Schema::dropIfExists('contact_reviews');
        Schema::dropIfExists('vouchers');
        Schema::dropIfExists('taps');
    }
};
