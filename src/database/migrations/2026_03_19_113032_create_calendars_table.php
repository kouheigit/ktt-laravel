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
        Schema::create('calendars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->date('date')->comment('対象日');
            $table->date('start_date')->nullable()->comment('期間開始日');
            $table->date('end_date')->nullable()->comment('期間終了日');
            $table->integer('status')->default(1)->comment('1:予約可,2:予約中,3予約済,9:休業');

            //検索用のindex(hotel_idがselectでホテルを選ぶ、dateは予約日
            $table->index(['hotel_id','date']);
            //履歴表示のindex
            $table->index(['user_id','start_date']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendars');
    }
};



