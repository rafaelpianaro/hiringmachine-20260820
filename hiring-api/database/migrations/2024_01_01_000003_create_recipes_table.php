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
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->json('ingredients');
            $table->json('instructions');
            $table->integer('prep_time')->nullable()->comment('Preparation time in minutes');
            $table->integer('cook_time')->nullable()->comment('Cooking time in minutes');
            $table->integer('servings')->default(4);
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->string('category');
            $table->string('image')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            $table->index('user_id');
            $table->index('category');
            $table->index('is_published');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
