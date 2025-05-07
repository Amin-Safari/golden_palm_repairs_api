<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('category_descriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_category_id')->constrained()->onDelete('cascade');
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->boolean('is_visible_to_owner')->default(true);
            $table->boolean('is_visible_to_technician')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('category_descriptions');
    }
};
