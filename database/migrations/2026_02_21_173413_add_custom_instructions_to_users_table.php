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
            $table->text('custom_instructions_about')->nullable()->after('last_model');
            $table->text('custom_instructions_behavior')->nullable()->after('custom_instructions_about');
            $table->text('custom_instructions_commands')->nullable()->after('custom_instructions_behavior');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'custom_instructions_about',
                'custom_instructions_behavior',
                'custom_instructions_commands',
            ]);
        });
    }
};
