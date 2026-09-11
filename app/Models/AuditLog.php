<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $table = 'audit_logs';

    protected $fillable = [
        'user_id',
        'username',
        'action',
        'ip_address',
        'user_agent',
        'login_at',
        'logout_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
