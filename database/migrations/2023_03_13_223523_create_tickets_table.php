<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('number');
            $table->text('comment')->nullable();
            $table->integer('priority')->default(0);
            $table->bigInteger('service_id')->unsigned();
            $table->integer('status_id')->unsigned();
            $table->bigInteger('user_id')->nullable()->unsigned();
            $table->timestamp('created_at');
            $table->timestamp('invited_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedBigInteger('client_id');

            $table->foreign('service_id')->references('id')->on('services');
            $table->foreign('status_id')->references('id')->on('statuses');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('client_id')->references('id')->on('clients');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
