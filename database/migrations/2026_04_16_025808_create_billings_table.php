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
    Schema::create('billings', function (Blueprint $table) {
        $table->id();
        $table->foreignId('appointment_id')->constrained()->cascadeOnDelete();
        $table->foreignId('patients_id')->constrained()->cascadeOnDelete();
        $table->decimal('amount', 10, 2);
        $table->string('status')->default('unpaid');
        $table->text('description')->nullable();
        $table->date('due_date')->nullable();
        $table->string('payment_method')->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billings');
    }
};
