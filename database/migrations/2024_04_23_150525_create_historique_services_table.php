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
        Schema::create('historique_services', function (Blueprint $table) {
            $table->uuid('id_service_history')->primary();
            $table->timestamps();
            $table->string('notes')->nullable();
            $table->datetime('date_rdv');
            $table->datetime('date');
            $table->integer('amount_paid');
            $table->string('hairstyle_name');
            $table->string('image')->nullable();
            $table->string('duration')->nullable();
            $table->string('status')->nullable()->default('en attente')->check('status IN ("en attente", "modifié", "terminé" , "Annulé")');
            $table->uuid('salon_id');
            $table->foreign('salon_id')->references('id_salon')->on('salons')->onDelete('cascade');
            $table->uuid('appointment_id');
            $table->uuid('client_id');
            $table->foreign('client_id')->references('id_client')->on('clients')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historique_services');
    }
};
