<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jenis_produk', function (Blueprint $table) {
            $table->unsignedBigInteger('id_produk_pusat')->nullable()->after('id');
           // $table->foreign('id_produk_pusat')->references('id')->on('produk_pusat')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('jenis_produk', function (Blueprint $table) {
          //  $table->dropForeign(['id_produk_pusat']);
            $table->dropColumn('id_produk_pusat');
        });
    }
};
