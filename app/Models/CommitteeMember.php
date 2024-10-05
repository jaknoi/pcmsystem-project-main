<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommitteeMember extends Model
{
    use HasFactory;

    protected $table = 'committee_member';

    protected $fillable = [
        'member_name',
        'member_position',
        'info_id',
    ];

    public function info()
    {
        return $this->belongsTo(Info::class, 'info_id', 'id');
    }
}