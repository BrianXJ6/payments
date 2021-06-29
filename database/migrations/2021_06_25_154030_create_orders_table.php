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
            $table->enum('payment_type', ['pix', 'credit_card', 'banking_billet']);
            $table->enum('status', ['new', 'waiting', 'paid', 'active', 'unpaid', 'refunded', 'contested', 'canceled', 'settled', 'link', 'expired', 'up_to_date', 'finished', 'ATIVA', 'CONCLUIDA', 'REMOVIDA_PELO_USUARIO_RECEBEDOR', 'REMOVIDA_PELO_PSP']);
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

            // PIX data
            $table->string('txid')->nullable()->default(null);
            $table->string('qrcode')->nullable()->default(null);
            $table->text('imagemQrcode')->nullable()->default(null);

            // Card data
            $table->string('card_last_for_digits')->nullable()->default(null);
            $table->string('card_brand')->nullable()->default(null);
            $table->unsignedTinyInteger('card_installments')->nullable()->default(null);
            $table->decimal('card_installment_value', 8, 2)->nullable()->default(null);
            $table->decimal('card_charge_total', 8, 2)->nullable()->default(null);

            // Billet data
            $table->string('billet_barcode')->nullable()->default(null);
            $table->string('billet_link')->nullable()->default(null);
            $table->string('billet_pdf')->nullable()->default(null);

            // Others
            $table->dateTime('paid_at')->nullable()->default(null);
            $table->date('expire_at')->nullable()->default(null);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down() {
        Schema::dropIfExists('orders');
    }
}
