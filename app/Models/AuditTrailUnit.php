<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditTrailUnit extends Model
{
    use HasFactory;

    protected $table = 'audit_trailunit';

    protected $fillable = [
        'user_id',
        'username',
        'action',
        'actiondata',
        'ip_address',
        'user_agent',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
