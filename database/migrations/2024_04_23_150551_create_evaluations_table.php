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
        Schema::create('evaluations', function (Blueprint $table) {
            $table->uuid('id_review')->primary();
            $table->timestamps();
            $table->text('comment')->nullable();
            $table->integer('note');
            $table->dateTime('date');
            $table->uuid('service_history_id');
            $table->foreign('service_history_id')->references('id_service_history')->on('historique_services')->onDelete('cascade');


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
