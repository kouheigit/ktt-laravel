<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHotelsTable extends Migration
{
    public function up()
    {
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('ホテル名');
            $table->string('address')->nullable()->comment('住所');
            $table->text('description')->nullable()->comment('説明');
            $table->integer('status')->default(1)->comment('1:有効,0:無効');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hotels');
    }
}

