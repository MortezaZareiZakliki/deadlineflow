<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

#[Fillable(['user_id', 'name', 'description', 'priority', 'start_date', 'deadline'])]
class Project extends Model implements Auditable
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $casts = [
        'start_date' => 'date',
        'deadline' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function versions()
    {
        return $this->hasMany(Version::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}