<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservationsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            //外部キー
            $table->foreignId('hotel_id')->constrained();
            $table->foreignId('user_id')->constrained()->comment('予約者');
            $table->foreignId('owner_id')->nullable()->constrained();
            $table->foreignId('invitation_id')->nullable()->constrained();


            // 宿泊情報
            $table->date('checkin_date')->comment('チェックイン日');
            $table->date('checkout_date')->comment('チェックアウト日');
            $table->time('checkin_time')->nullable()->comment('チェックイン時刻');
            $table->time('checkout_time')->nullable()->comment('チェックアウト時刻');
            $table->integer('day')->default(1)->comment('宿泊日数');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};


