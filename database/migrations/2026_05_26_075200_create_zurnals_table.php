<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zurnals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gramata_id')->constrained('books')->onDelete('cascade');
            $table->string('operacija');
            $table->string('lauks');
            $table->text('veca_vertiba')->nullable();
            $table->text('jauna_vertiba')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zurnals');
    }
};
