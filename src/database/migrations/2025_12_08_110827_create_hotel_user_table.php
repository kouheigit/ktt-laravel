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
        Schema::create('hotel_user', function (Blueprint $table) {
            $table->id();
            $table->foreign('hotel_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            // 複合ユニークキー
            $table->unique(['hotel_id','user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('hotel_user');
    }
};

