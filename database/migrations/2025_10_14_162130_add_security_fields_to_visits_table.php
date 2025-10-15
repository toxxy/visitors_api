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
            $table->boolean('is_invalid')->default(false);
            $table->string('invalid_reason')->nullable();
            $table->dateTime('checked_in_at')->nullable();
            $table->dateTime('checked_out_at')->nullable();
            $table->integer('check_in_count')->default(0);
            $table->integer('check_out_count')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->dropColumn([
                'is_invalid',
                'invalid_reason',
                'checked_in_at',
                'checked_out_at',
                'check_in_count',
                'check_out_count'
            ]);
        });
    }
};
