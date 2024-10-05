<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'product';

    protected $fillable = [
        'product_type',
        'product_name',
        'quantity',
        'unit',
        'product_price',
        'info_id',
    ];

    public function info()
    {
        return $this->belongsTo(Info::class, 'info_id', 'id');
    }
}