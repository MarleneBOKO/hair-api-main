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
        Schema::create('rendez_vous', function (Blueprint $table) {
            $table->uuid('id_appointment')->primary();
            $table->timestamps();
            $table->datetime('date_and_time');
            $table->string('status')->nullable()->default('en attente')->check('status IN ("en attente", "modifié", "confirmed", "terminé" , "Annulé")');
            $table->string('notes')->nullable();
            $table->double('total_amount');
            $table->string('payment_method');
            $table->uuid('hairstyle_type_id');
            $table->time('duration')->nullable();
            $table->uuid('client_id');
            $table->foreign('client_id')->references('id_client')->on('clients')->onDelete('cascade');
            $table->uuid('salon_id');
            $table->foreign('salon_id')->references('id_salon')->on('salons')->onDelete('cascade');
            $table->foreign('hairstyle_type_id')->references('id_hairstyle_type')->on('type_coiffures')->onDelete('cascade');

           


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rendez_vous');
    }
};
