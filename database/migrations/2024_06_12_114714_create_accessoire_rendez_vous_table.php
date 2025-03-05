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
        Schema::create('accessoire_rendez_vous', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->uuid('accessory_id');
            $table->uuid('appointment_id');
            $table->foreign('appointment_id')->references('id_appointment')->on('rendez_vous')->onDelete('cascade');
            $table->foreign('accessory_id')->references('id_accessory')->on('accessoires')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accessoire_rendez_vous');
    }
};
