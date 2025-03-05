<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id_transaction');
            $table->string('performed_at')->nullable();
            $table->string('received_at')->nullable();
            $table->string('status');
            $table->decimal('amount', 10, 2);
            $table->string('source');
            $table->string('source_common_name')->nullable();
            $table->decimal('fees', 10, 2)->nullable();
            $table->decimal('net', 10, 2)->nullable();
            $table->string('externalTransactionId')->unique();
            $table->string('acc_fullname');
            $table->string('acc_phone');
            $table->string('acc_email')->nullable();
            $table->string('acc_person')->nullable();
            $table->string('transactionId')->unique();
            $table->string('transaction_object');
            $table->uuid('appointment_id');
            $table->timestamps();
            $table->foreign('appointment_id')->references('id_appointment')->on('rendez_vous')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
