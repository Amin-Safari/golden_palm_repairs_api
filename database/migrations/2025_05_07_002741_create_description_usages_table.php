<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('description_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_description_id')->constrained()->onDelete('cascade');
            $table->foreignId('repair_id')->constrained()->onDelete('cascade');
            $table->decimal('price_at_time', 10, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('description_usages');
    }
};
