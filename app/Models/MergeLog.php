<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class MergeLog extends Model { public $timestamps=false; protected $fillable=['user_id','direction','source_repo','source_branch','dest_repo','dest_branch','pr_link','merge_status','deploy_status','deploy_output','created_at']; public function user(){ return $this->belongsTo(User::class,'user_id'); } }
