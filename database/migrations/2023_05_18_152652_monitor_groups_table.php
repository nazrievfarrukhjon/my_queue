<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monitor_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('series')->default('A');
            $table->integer('queue_number')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monitor_groups');
    }
};
