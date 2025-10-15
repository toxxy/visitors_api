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
        Schema::table('visits', function (Blueprint $table) {
            // Cambiar el enum para incluir 'confirmed'
            $table->enum('status', ['scheduled', 'confirmed', 'arrived', 'completed', 'cancelled'])->default('scheduled')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            // Revertir al enum original
            $table->enum('status', ['scheduled', 'arrived', 'completed', 'cancelled'])->default('scheduled')->change();
        });
    }
};
