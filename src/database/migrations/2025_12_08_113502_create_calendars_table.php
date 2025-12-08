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

/*
 * Step 1-3: カレンダー・予約関連（Day 2-3）
1. calendarsテーブル
php artisan make:migration create_calendars_table

【実行結果】 <?php
php artisan make:migration create_calendars_table
<?php
use Illuminate\Database\Migrations\Migration;

use Illuminate\Database\Schema\Blueprint;

use Illuminate\Support\Facades\Schema;

class CreateCalendarsTable extends Migration
{
    public function up()
    {
        Schema::create('calendars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->date('date')->comment('対象日');
            $table->date('start_date')->nullable()->comment('期間開始日');
            $table->date('end_date')->nullable()->comment('期間終了日');
            $table->integer('status')->default(1)->comment('1:予約可, 2:予約中, 3:予約済, 9:休業');
            $table->timestamps();

            // インデックス
            $table->index(['hotel_id', 'date']);
            $table->index(['user_id', 'start_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('calendars');
    }
}

 */
