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


            // ゲスト情報
            $table->string('name')->nullable()->comment('代表者名');
            $table->string('adult')->default(0)->comment('大人人数');
            $table->integer('child')->default(0)->comment('子供人数');
            $table->integer('dog')->default(0)->comment('犬頭数');
            $table->text('note')->nullable()->comment('備考');


            // 施設情報
            $table->string('room_key', 50)->nullable()->comment('入室番号');
            $table->string('upload')->nullable()->comment('アップロードファイル');

            // 決済・ステータス
            $table->integer('payment')->default(0)->comment('0:現地払い, 1:クレジット');
            $table->integer('status')->default(1)->comment('ステータス');


            //論理削除
            $table->softDeletes();
            $table->timestamps();

            // インデックス
            $table->index(['user_id', 'status']);
            $table->index(['checkin_date', 'status']);
            $table->index('owner_id');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('reservations');
    }
}


