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
        Schema::create('stocks', function (Blueprint $table) {
            $table->uuid('id_product')->primary();
            $table->timestamps();
            $table->string('product_name');
            $table->integer('quantity');
            $table->integer('reorder_level')->nullable();
            $table->string('description')->nullable();
            $table->date('addition_date');
            $table->date('last_modification_date');
            $table->uuid('salon_id');
            $table->foreign('salon_id')->references('id_salon')->on('salons')->onDelete('cascade');
            $table->uuid('fournisseur_id');
            $table->foreign('fournisseur_id')->references('id_supplier')->on('fournisseurs')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
