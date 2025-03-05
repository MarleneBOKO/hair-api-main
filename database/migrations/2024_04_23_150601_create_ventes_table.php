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
        Schema::create('ventes', function (Blueprint $table) {
            $table->uuid('id_sale')->primary();
            $table->timestamps();
            $table->string('notes')->nullable();
            $table->dateTime('date');
            $table->double('total_amount');
            $table->string('payment_method');
            $table->uuid('hairstyle_type_id');
            $table->foreign('hairstyle_type_id')->references('id_hairstyle_type')->on('type_coiffures')->onDelete('cascade');
            $table->uuid('client_id');
            $table->foreign('client_id')->references('id_client')->on('clients')->onDelete('cascade');
            $table->uuid('salon_id');
            $table->foreign('salon_id')->references('id_salon')->on('salons')->onDelete('cascade');
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id_user')->on('users')->onDelete('cascade');


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventes');
    }
};
