<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class task extends Model
{
    use HasFactory;

    protected $table = 'tasks';
    protected $primaryKey = 'taskId'; // Указываем поле taskId как первичный ключ
    public $incrementing = false; // Устанавливаем auto-increment в значение false
    protected $keyType = 'string'; // Устанавливаем тип ключа как строка
    protected $fillable = ['taskId','title', 'completed', 'groupId', 'groupTitle', 'userId', 'created_at', 'updated_at'];
}
