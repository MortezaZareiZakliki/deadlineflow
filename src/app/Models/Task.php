<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

#[Fillable(['project_id', 'user_id', 'version_id', 'title', 'description', 'status', 'priority', 'deadline', 'deadline_notified_at'])]
class Task extends Model implements Auditable
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $fillable = ['project_id', 'user_id', 'version_id', 'title', 'description', 'status', 'priority', 'deadline', 'deadline_notified_at'];

    protected $casts = [
        'deadline'             => 'date:Y-m-d',
        'deadline_notified_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function version()
    {
        return $this->belongsTo(Version::class);
    }
}