<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('repo_branch_matches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_a_id');
            $table->unsignedBigInteger('branch_b_id');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->unique(['branch_a_id','branch_b_id'], 'uniq_branch_ids');
            $table->foreign('branch_a_id')->references('id')->on('repo_branches')->cascadeOnDelete();
            $table->foreign('branch_b_id')->references('id')->on('repo_branches')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repo_branch_matches');
    }
};


