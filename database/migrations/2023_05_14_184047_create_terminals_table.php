<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('terminals', function (Blueprint $table) {
            $table->uuid('terminal_uuid')->unique();
            $table->string('name');
            $table->string('token');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('terminals');
    }
};