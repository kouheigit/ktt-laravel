<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreateCalendarsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('calendars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('hotel_id')->nullable()->constrained()->onDelete('cascade');
            $table->date('date')->comment('対象日');
            $table->date('start_date')->nullable()->comment('期間開始日');
            $table->date('end_date')->nullable()->comment('期間終了');
            $table->integer('status')->default(1)->comment('1:予約可,2予約中,3予約済,9:休業');
            $table->timestamps();

            //インデックス
            $table->index(['hotel_id','date']);
            $table->index(['user_id','start_date']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('calendars');
    }
};



