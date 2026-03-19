<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUsersTable extends Migration
{
    public function up(){
        Schema::table('users', function (Blueprint $table) {
            // 会員ID
            $table->string('member_id')->nullable()->unique()->after('id');

            $table->string('last_name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_kana')->nullable();
            $table->string('first_kana')->nullable();
            $table->string('zip1',3)->nullable();
            $table->string('zip2',4)->nullable();
            $table->string('address1')->nullable();
            $table->string('address2')->nullable();
            $table->string('tel',20)->nullable();

            // 会社情報
            $table->string('company_name')->nullable();
            $table->string('company_kana')->nullable();
            $table->string('company_zip1',3)->nullable();
            $table->string('company_zip2',4)->nullable();
            $table->string('company_address1')->nullable();
            $table->string('company_address2')->nullable();
            $table->string('company_tel',20)->nullable();
            $table->string('company_fax',20)->nullable();

            //送付先情報
            $table->string('send_name')->nullable();
            $table->string('send_kana')->nullable();
            $table->string('send_zip1',3)->nullable();
            $table->string('send_zip2',4)->nullable();
            $table->string('send_address1')->nullable();
            $table->string('send_address2')->nullable();
            $table->string('send_tel',20)->nullable();

            //システム情報
            $table->integer('type')->default(1)->comment('1:一般,2:オーナー');
            $table->integer('agree')->default(0)->comment('利用規約同意');
            $table->integer('status')->default(1)->comment('1:有効,0:無効');
            $table->foreignId('parent_user_id')->nullable()->constrained('users')->onDelete('set null')->comment('親ユーザーID(オーナーの場合)');

            //論理削除
            $table->softDeletes();

        });
    }
    public function down(){
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'member_id', 'last_name', 'first_name', 'last_kana', 'first_kana',
                'zip1', 'zip2', 'address1', 'address2', 'tel',
                'company_name', 'company_kana', 'company_zip1', 'company_zip2',
                'company_address1', 'company_address2', 'company_tel', 'company_fax',
                'send_name', 'send_kana', 'send_zip1', 'send_zip2',
                'send_address1', 'send_address2', 'send_tel',
                'type', 'agree', 'status', 'user_id', 'deleted_at'
            ]);

        });
    }
}



