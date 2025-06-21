<?php

namespace App\Models;

use APp\Enums\Document\Status;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'status',
        'options',
        'file',
        'type',
        'ai_model',
        'text_extraction',
        'summary',
        'translations',
        'error',
    ];

    protected $casts = [
        'options' => 'array',
        'status' => Status::class,
    ];
}
