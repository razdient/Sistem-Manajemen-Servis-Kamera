<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('service_visit_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedBigInteger('amount');
            $table->string('payment_type');
            $table->string('payment_method');
            $table->dateTime('paid_at');
            $table->text('note')->nullable();

            $table->timestamps();

            $table->index(['service_visit_id', 'paid_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
