<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('predictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->json('insights'); // Stores AI predictions as JSON
            $table->timestamps();

            // One prediction per user
            $table->unique('user_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('predictions');
    }
};
