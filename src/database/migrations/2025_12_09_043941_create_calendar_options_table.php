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
        Schema::create('calendar_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calendar_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('body')->nullable();
            $table->integer('sort')->default(0);
            $table->integer('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('calendar_options');
    }
};

