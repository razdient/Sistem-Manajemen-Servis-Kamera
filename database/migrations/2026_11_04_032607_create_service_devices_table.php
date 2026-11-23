<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_devices', function (Blueprint $table) {
            $table->id();

            $table->foreignId('service_visit_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('device_type');
            $table->string('brand');
            $table->string('model');

            $table->text('complaint');
            $table->text('physical_condition')->nullable();
            $table->text('accessories')->nullable();

            $table->string('status')->default('received');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_devices');
    }
};
