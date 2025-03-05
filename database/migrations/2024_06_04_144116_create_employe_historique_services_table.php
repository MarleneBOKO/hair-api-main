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
        Schema::create('employe_historique_services', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->uuid('employe_id');
            $table->uuid('service_history_id');
            $table->foreign('service_history_id')->references('id_service_history')->on('historique_services')->onDelete('cascade');
            $table->foreign('employe_id')->references('id_employe')->on('employes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employe_historique_services');
    }
};
