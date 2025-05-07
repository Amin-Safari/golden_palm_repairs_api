<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up()
{
    Schema::create('repairs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('customer_id')->constrained('users');
        $table->foreignId('technician_id')->nullable()->constrained('users');
        $table->foreignId('device_category_id')->constrained();
        $table->string('device_name');
        $table->text('device_problem');
        $table->text('appearance_details')->nullable();
        $table->text('repair_details')->nullable();
        $table->decimal('price', 15, 2)->nullable();
        $table->string('status')->default('received');
        $table->string('verification_code')->nullable();
        $table->dateTime('received_at');
        $table->dateTime('delivered_at')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repairs');
    }
};
