<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\Seller;
use App\Models\CommitteeMember;
use App\Models\Bidder;
use App\Models\Inspector;
use App\Models\More;

class Info extends Model
{
    use HasFactory;

    protected $table = 'info';

    protected $fillable = [
        'methode_name',
        'reason_description',
        'office_name',
        'date',
        'devilvery_time',
    ];

    // ความสัมพันธ์กับโมเดลอื่น ๆ
    public function products()
    {
        return $this->hasMany(Product::class, 'info_id', 'id');
    }

    public function sellers()
    {
        return $this->hasMany(Seller::class, 'info_id', 'id');
    }

    public function committeemembers()
    {
        return $this->hasMany(CommitteeMember::class, 'info_id', 'id');
    }

    public function bidders()
    {
        return $this->hasMany(Bidder::class, 'info_id', 'id');
    }

    public function inspectors()
    {
        return $this->hasMany(Inspector::class, 'info_id', 'id');
    }
    public function mores()
    {
        return $this->hasMany(More::class, 'info_id', 'id');
    }
}









