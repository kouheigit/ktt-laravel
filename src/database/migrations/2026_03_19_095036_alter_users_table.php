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

            // 会社情報
            $table->string('company_name')->nullable();
            $table->string('company_kana')->nullable();
            $table->string('company_zip1',3)->nullable();
            $table->string('company_zip2',4)->nullable();
            $table->string('company_address1')->nullable();
            $table->string('company_address2')->nullable();
            $table->string('company_tex',20)->nullable();
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
            $table->integer('user_id')->nullable()->comment('親ユーザーID(オーナーの場合)');

            //論理削除
            $table->softDeletes();



        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'member_id','last_name','first_name','last_kana','first_kana',
                'zip1','zip2','address1','address2','tel',
                'company_name','company_kana','company_zip1','company_zip2',
                'company_address1','company_address2','compay_tel','company_fax',
                'send_name','send_kana','send_zip1','send_tel','type','agree',
                'status','user_id','deleted_at',
            ]);
        });
    }
};

