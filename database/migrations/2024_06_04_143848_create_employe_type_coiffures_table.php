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
        Schema::create('employe_type_coiffures', function (Blueprint $table) {
            $table->uuid();
            $table->timestamps();      
            $table->time('duration');
            $table->uuid('employe_id');
            $table->uuid('hairstyle_type_id');
            $table->foreign('hairstyle_type_id')->references('id_hairstyle_type')->on('type_coiffures')->onDelete('cascade');
            $table->foreign('employe_id')->references('id_employe')->on('employes')->onDelete('cascade');



        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employe_type_coiffures');
    }
};
