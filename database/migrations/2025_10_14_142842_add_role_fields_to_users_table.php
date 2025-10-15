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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin_master', 'admin_site', 'security', 'manager'])->default('security');
            $table->unsignedBigInteger('site_id')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            
            $table->foreign('site_id')->references('id')->on('sites')->onDelete('set null');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['site_id']);
            $table->dropForeign(['department_id']);
            $table->dropColumn(['role', 'site_id', 'department_id']);
        });
    }
};
