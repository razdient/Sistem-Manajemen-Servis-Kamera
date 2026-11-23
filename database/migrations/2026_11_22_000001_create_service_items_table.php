<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('service_device_id')
                ->constrained()
                ->cascadeOnDelete();

            // Jenis rincian: jasa atau suku cadang.
            $table->string('item_type')->default('service');

            $table->string('description');

            $table->unsignedInteger('quantity')->default(1);

            // Harga disimpan dalam rupiah (bilangan bulat).
            $table->unsignedBigInteger('unit_price')->default(0);
            $table->unsignedBigInteger('unit_cost')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_items');
    }
};
