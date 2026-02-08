<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepoBranchMatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_a_id', 'branch_b_id', 'created_by',
    ];

    public function branchA()
    {
        return $this->belongsTo(RepoBranch::class, 'branch_a_id');
    }

    public function branchB()
    {
        return $this->belongsTo(RepoBranch::class, 'branch_b_id');
    }
}


