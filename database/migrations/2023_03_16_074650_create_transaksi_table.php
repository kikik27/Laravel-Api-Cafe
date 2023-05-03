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
        Schema::create('transaksi', function (Blueprint $table) {
            $table->string('id_transaksi')->primary()->unique();
            $table->index('id_transaksi');
            $table->dateTime('tanggal');
            $table->integer('kasir');
            $table->index('kasir');
            $table->integer('id_meja');
            $table->index('id_meja');
            $table->string('pelanggan');
            $table->enum('status',['belum_bayar','lunas'])->default('belum_bayar');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaksi');
    }
};
