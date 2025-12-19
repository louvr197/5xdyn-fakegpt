<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FailedModel extends Model
{
    protected $fillable = [
        'model_id',
        'last_error',
        'last_failed_at',
        'failure_count',
    ];

    protected function casts(): array
    {
        return [
            'last_failed_at' => 'datetime',
        ];
    }
}
