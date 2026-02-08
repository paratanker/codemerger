<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('repo_branches', function (Blueprint $table) {
            $table->id();
            $table->string('workspace');
            $table->string('repo');
            $table->string('branch');
            $table->timestamps();
            $table->unique(['workspace', 'repo', 'branch']);
            $table->index(['workspace', 'repo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repo_branches');
    }
};


