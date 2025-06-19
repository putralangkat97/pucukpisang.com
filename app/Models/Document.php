<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'original_name',
        'storage_path',
        'file_type',
        'pages',
        'status',
        'operations',
        'operation_params',
        'result',
        'ai_model',
        'token_usage',
        'cost'
    ];

    protected $casts = [
        'operations' => 'array',
        'operation_params' => 'array',
        'result' => 'array'
    ];
}
