<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('food_pack_norms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staple_id')->constrained()->cascadeOnDelete();
            $table->decimal('pack_amount', 10, 3);
            $table->string('pack_unit');
            $table->string('label_da')->nullable();
            $table->string('source');
            $table->string('confidence');
            $table->date('last_verified')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('staple_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_pack_norms');
    }
};
