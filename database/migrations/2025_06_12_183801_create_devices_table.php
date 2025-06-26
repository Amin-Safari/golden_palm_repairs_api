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
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users');
            $table->foreignId('technician_id')->nullable()->constrained('users');
            $table->foreignId('device_category_id')->constrained('device_categories');
            $table->string('device_name');
            $table->text('device_problem')->nullable();
            $table->text('repair_details')->nullable();
            $table->decimal('prepaid', 15, 2)->nullable();
            $table->decimal('price', 15, 2)->nullable();
            $table->decimal('repair_price', 15, 2)->nullable();
            $table->string('status')->default('received')->comment('received,in_progress,delivered,canceled');
            $table->string('repair_status')->default('unpaid')->comment('unpaid,pay');
            $table->string('verification_code')->nullable();
            $table->dateTime('received_at')->nullable();
            $table->dateTime('delivered_at')->nullable();
            $table->dateTime('delivered_real_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
