<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepoBranch extends Model
{
    use HasFactory;

    protected $fillable = [
        'workspace',
        'repo',
        'branch',
    ];
}


