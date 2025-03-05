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
        Schema::create('virtual_comptes', function (Blueprint $table) {
            $table->id();
            $table->uuid('client_id');
            $table->foreign('client_id')->references('id_client')->on('clients')->onDelete('cascade');
            $table->double('somme')->defaut(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('virtual_comptes');
    }
};
