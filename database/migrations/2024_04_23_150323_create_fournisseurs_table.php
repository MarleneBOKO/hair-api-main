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
        Schema::create('fournisseurs', function (Blueprint $table) {
            $table->uuid('id_supplier')->primary();
            $table->timestamps();
            $table->string('supplier_name');
            $table->string('address')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('contact')->nullable();
            $table->text('notes')->nullable();
            $table->uuid('salon_id');
            $table->foreign('salon_id')->references('id_salon')->on('salons')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fournisseurs');
    }
};
