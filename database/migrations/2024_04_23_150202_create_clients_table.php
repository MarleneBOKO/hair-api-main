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
        Schema::create('clients', function (Blueprint $table) {
            $table->uuid('id_client')->primary();
            $table->timestamps();
            $table->string('name');
            $table->string('address')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('email');
            $table->date('birth_date')->nullable();
            $table->string('gender')->nullable();
            $table->text('notes')->nullable();
            $table->date('first_visit_date')->nullable();
            $table->date('last_visit_date')->nullable();
            $table->uuid('user_id');
            $table->uuid('salon_id');
            $table->foreign('salon_id')->references('id_salon')->on('salons')->onDelete('cascade');
            $table->foreign('user_id')->references('id_user')->on('users')->onDelete('cascade');
           

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
