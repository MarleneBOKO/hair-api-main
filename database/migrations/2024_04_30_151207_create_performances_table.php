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
        Schema::create('performances', function (Blueprint $table) {
            $table->uuid('id_performance')->primary();
            $table->timestamps();
            $table->string('revenue_generated');
            $table->string('clients_served');
            $table->date('date');
            $table->uuid('employe_id');
            $table->foreign('employe_id')->references('id_employe')->on('employes')->onDelete('cascade');
            $table->uuid('service_history_id');
            $table->foreign('service_history_id')->references('id_service_history')->on('historique_services')->onDelete('cascade');



        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performances');
    }
};
