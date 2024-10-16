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
use App\Enums\PackageNameEnum;
use App\Enums\PackageTypeEnum;

return new class extends Migration {
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
            $table->enum('name', PackageNameEnum::values())->default(PackageNameEnum::standard->value);
            $table->enum('type', PackageTypeEnum::values())->default(PackageTypeEnum::month->value);
            $table->integer('first_price');
            $table->integer('second_price');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('venue_id');
            $table->unsignedBigInteger('package_id');
            $table->string('xendit_plan_id')->nullable();
            $table->string('amount')->nullable();
            $table->boolean('status')->defaul(false);
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
            $table->string('wa_number')->nullable();
            $table->string('wa_instanceid')->nullable();
            $table->string('wa_accesstoken')->nullable();
            $table->string('payment_public')->nullable();
            $table->string('payment_secret')->nullable();
            $table->string('payment_webhook_secret')->nullable();
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
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('contacts');
        Schema::dropIfExists('contact_reviews');
        Schema::dropIfExists('vouchers');
        Schema::dropIfExists('taps');
    }
};
