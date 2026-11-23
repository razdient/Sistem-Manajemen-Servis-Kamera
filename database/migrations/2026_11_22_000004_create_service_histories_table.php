<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('service_device_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('old_status')->nullable();
            $table->string('new_status');
            $table->text('note')->nullable();

            $table->timestamps();

            $table->index(['service_device_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_histories');
    }
};
