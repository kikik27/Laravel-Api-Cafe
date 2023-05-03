<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailTransaksi extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'detail_transaksi';
    public $primaryKey = 'detail_transaksi';
    public $fillable=['id_detail_transaksi','id_transaksi','id_menu','qty','harga','total'];
}
