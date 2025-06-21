<?php

namespace App\Models;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Model;

class Audio extends Model
{
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    protected $table = 'audios';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'status',
        'source_type',
        'source_path',
        'options',
        'ai_model',
        'transcript',
        'summary',
        'translations',
        'error',
    ];

    protected $casts = [
        'options' => 'array',
        'status' => Status::class,
    ];
}
