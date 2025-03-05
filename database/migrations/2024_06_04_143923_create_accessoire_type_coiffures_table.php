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
        Schema::create('accessoire_type_coiffures', function (Blueprint $table) {
            $table->uuid();
            $table->timestamps();
            $table->uuid('accessory_id');
            $table->uuid('hairstyle_type_id');
            $table->foreign('hairstyle_type_id')->references('id_hairstyle_type')->on('type_coiffures')->onDelete('cascade');
            $table->uuid('salon_id');
            $table->foreign('salon_id')->references('id_salon')->on('salons')->onDelete('cascade');
            $table->foreign('accessory_id')->references('id_accessory')->on('accessoires')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accessoire_type_coiffures');
    }
};
