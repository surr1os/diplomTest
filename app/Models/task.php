<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class task extends Model
{
    use HasFactory;

    protected $table = 'tasks';
    protected $primaryKey = 'taskId';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['taskId','title', 'completed', 'group_priority', 'groupId', 'groupTitle', 'userId', 'created_at', 'updated_at', 'execution_date'];
}
