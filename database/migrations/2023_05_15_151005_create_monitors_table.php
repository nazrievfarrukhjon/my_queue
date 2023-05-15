<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('monitors', function (Blueprint $table) {
            $table->id();
            $table->integer('counter')->default(0);
            $table->string('group', 100);
            $table->string('name', 100);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monitors');
    }
};
