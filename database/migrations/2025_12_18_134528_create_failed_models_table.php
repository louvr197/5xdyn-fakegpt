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
        Schema::create('failed_models', function (Blueprint $table) {
            $table->id();
            $table->string('model_id')->unique();
            $table->text('last_error')->nullable();
            $table->timestamp('last_failed_at');
            $table->integer('failure_count')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('failed_models');
    }
};
