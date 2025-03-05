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
        Schema::create('employes', function (Blueprint $table) {
            $table->uuid('id_employe')->primary();
            $table->timestamps();
            $table->string('name');
            $table->string('skills')->check('skills IN ("expert", "débutant", "moyen" )');
            $table->string('description')->nullable();
            $table->string('image')->nullable();
            $table->string('hiring_date');
            $table->string('departure_date')->nullable();
            $table->string('work_hours');
            $table->double('salary');
            $table->string('status');
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id_user')->on('users')->onDelete('cascade');
            $table->uuid('salon_id');
            $table->foreign('salon_id')->references('id_salon')->on('salons')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employes');
    }
};
