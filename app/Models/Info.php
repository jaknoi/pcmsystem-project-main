<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}

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

class Seller extends Model
{
    use HasFactory;

    protected $table = 'seller';

    protected $fillable = [
        'seller_name',
        'address',
        'taxpayer_number',
        'reference_documents',
        'info_id',
    ];

    public function info()
    {
        return $this->belongsTo(Info::class, 'info_id', 'id');
    }
}

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

class Bidder extends Model
{
    use HasFactory;

    protected $table = 'bidder';

    protected $fillable = [
        'bidder_name',
        'bidder_position',
        'info_id',
    ];

    public function info()
    {
        return $this->belongsTo(Info::class, 'info_id', 'id');
    }
}

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


