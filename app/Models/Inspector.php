<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inspector extends Model
{
    use HasFactory;

    protected $table = 'inspector';

    protected $fillable = [
        'inspector_name',
        'inspector_position',
        'info_id',
    ];

    public function info()
    {
        return $this->belongsTo(Info::class, 'info_id', 'id');
    }

    
}