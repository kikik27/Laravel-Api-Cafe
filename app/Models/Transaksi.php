<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'transaksi';
    public $primaryKey = 'id_transaksi';
    public $fillable=['id_transaksi','tanggal','kasir','id_meja','pelanggan','total','status'];
}
