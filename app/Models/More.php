<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class More extends Model
{
    use HasFactory;

    protected $table = 'more';

    protected $fillable = [
        'price_list',
        'request_documents',
        'middle_price_first',
        'middle_price_second',
        'middle_price_third',
        'info_id',
    ];

    public function info()
    {
        return $this->belongsTo(Info::class, 'info_id', 'id');
    }

    
}