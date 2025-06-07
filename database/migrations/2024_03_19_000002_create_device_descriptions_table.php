<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('device_descriptions', function (Blueprint $table) {
            $table->id();
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->foreignId('category_id')->constrained('device_categories')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('device_descriptions');
    }
};