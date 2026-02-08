<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(){
        Schema::create('merge_logs', function (Blueprint $table){
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('direction',10);
            $table->string('source_repo');
            $table->string('source_branch');
            $table->string('dest_repo');
            $table->string('dest_branch');
            $table->string('pr_link')->nullable();
            $table->string('merge_status')->nullable();
            $table->string('deploy_status')->nullable();
            $table->text('deploy_output')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }
    public function down(){ Schema::dropIfExists('merge_logs'); }
};
