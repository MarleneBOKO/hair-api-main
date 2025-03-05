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
        Schema::create('salons', function (Blueprint $table) {
            $table->uuid('id_salon')->primary();
            $table->timestamps();
            $table->string('salon_name');
            $table->string('address')->nullable();
            $table->string('phone_number');
            $table->string('email');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->text('opening_hours')->nullable();
            $table->string('website')->nullable();
            $table->dateTime('creation_date');
            $table->dateTime('last_update_date');
            $table->uuid('user_id')->unique();
            $table->foreign('user_id')->references('id_user')->on('users')->onDelete('cascade');
            $table->string('longitude')->nullable();
            $table->string('latitude')->nullable();


        });
    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salons');
    }
};
