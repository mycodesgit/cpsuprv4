<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditTrailUserRole extends Model
{
    use HasFactory;

    protected $table = 'audit_trailuserrole';

    protected $fillable = [
        'username',
        'action',
        'actiondata',
        'ip_address',
        'user_agent',
    ];
}
