<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('url_checks', function (Blueprint $table) {
            $table->id();
            $table->integer('url_id');
            $table->integer('status_code');
            $table->string('h1')->nullable();
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->foreign('url_id')
                ->references('id')
                ->on('urls')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('url_checks');
    }
};
