<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campus extends Model
{
    use HasFactory;

    protected $table = 'campuses';

    protected $fillable = [
        'user_id',
        'campus_name',
        'campus_abbr',
        'status',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    public function purchaseRequests()
    {
        return $this->hasMany(PurchaseRequest::class, 'campus_id');
    }
}
