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
        Schema::table('users', function (Blueprint $table) {

            // 個人情報
            $table->string('member_id')->unique()->nullable()->after('id');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('first_kana')->nullable();
            $table->string('zip1',3)->nullable();
            $table->string('zip2', 4)->nullable();
            $table->string('address1')->nullable();
            $table->string('address2')->nullable();
            $table->string('tel',20)->nullable();

            /*
             *


             */
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
