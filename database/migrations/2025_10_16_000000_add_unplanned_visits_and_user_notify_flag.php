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
            $table->boolean('is_unplanned')->default(false)->after('notes');
            $table->string('visit_to')->nullable()->after('is_unplanned');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('notify_unplanned')->default(false)->after('department_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->dropColumn(['is_unplanned', 'visit_to']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('notify_unplanned');
        });
    }
};
