<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('library_contents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('type'); // article, video, pdf, etc.
            $table->text('description')->nullable();
            $table->string('access_level')->default('free'); // free, basic, premium
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('library_contents');
    }
};
