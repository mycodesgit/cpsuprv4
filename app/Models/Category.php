<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'categories';

    protected $fillable = [
        'user_id',
        'cstatus',
        'category_name',
        'isICT',
    ];
    /**
     * Cast attributes to native types.
     */
    protected $casts = [
        'cstatus' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function items()
    {
        return $this->hasMany(Item::class, 'category_id');
    }
}
