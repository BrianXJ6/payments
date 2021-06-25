<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration {

    public function up() {
        Schema::create('orders', function (Blueprint $table) {
            // Important data
            $table->id();
            $table->unsignedBigInteger('payment_id')->unique();
            $table->enum('payment_type', ['credit_card']);
            $table->enum('status', ['waiting']);
            $table->decimal('total', 8, 2);

            // Products
            $table->string('products')->nullable()->default(null);

            // User
            $table->string('user_email');
            $table->string('user_name');
            $table->string('user_document');
            $table->string('user_phone');
            $table->date('user_birth');

            // Address
            $table->string('address_zip');
            $table->string('address_street');
            $table->string('address_number')->nullable()->default(null);
            $table->string('address_district');
            $table->string('address_city');
            $table->string('address_state');

            // Card data
            $table->string('card_last_for_digits')->nullable()->default(null);
            $table->string('card_brand')->nullable()->default(null);
            $table->unsignedTinyInteger('installments')->nullable()->default(null);
            $table->decimal('installment_value', 8, 2)->nullable()->default(null);

            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('orders');
    }
}