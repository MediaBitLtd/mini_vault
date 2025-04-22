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
        Schema::create('vault_record_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vault_record_id')->constrained()->cascadeOnDelete();
            $table->foreignId('field_id')->constrained();
            $table->string('name', 50)->nullable();
            $table->string('uid')->unique();
            $table->mediumText('value');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vault_record_values');
    }
};
