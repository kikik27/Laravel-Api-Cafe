<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $incrementing = false; 
    protected $table = 'menu';
    protected $primaryKey = 'id_menu';
    protected $fillable=['id_menu','nama_menu','jenis','desc','gambar','harga'];
    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
{
    return asset('storage/gambar/menu/'.$this->gambar);
}
}
