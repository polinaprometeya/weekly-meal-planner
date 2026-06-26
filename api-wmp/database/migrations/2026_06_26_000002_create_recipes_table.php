<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name_da');
            $table->unsignedInteger('servings_default')->default(4);
            $table->text('instructions');
            $table->text('source_instructions_raw')->nullable();
            $table->string('source_type');
            $table->string('source_title')->nullable();
            $table->string('source_url')->nullable();
            $table->unsignedSmallInteger('publication_year')->nullable();
            $table->string('license');
            $table->string('attribution_text')->nullable();
            $table->unsignedSmallInteger('prep_minutes')->nullable();
            $table->unsignedSmallInteger('cook_minutes')->nullable();
            $table->json('tags')->nullable();
            $table->boolean('is_reviewed')->default(false)->index();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
