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
            $table->string('tone_style')->nullable()->after('custom_instructions_commands');
            $table->string('conciseness')->nullable()->after('tone_style');
            $table->string('titles_lists')->nullable()->after('conciseness');
            $table->string('warmth')->nullable()->after('titles_lists');
            $table->string('enthusiasm')->nullable()->after('warmth');
            $table->string('formality')->nullable()->after('enthusiasm');
            $table->string('emojis')->nullable()->after('formality');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'tone_style',
                'conciseness',
                'titles_lists',
                'warmth',
                'enthusiasm',
                'formality',
                'emojis',
            ]);
        });
    }
};
