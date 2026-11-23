<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_catalogs', function (Blueprint $table) {
            $table->id();

            // Jenis katalog: jasa atau suku cadang.
            $table->string('item_type')->index();

            // Kategori opsional, misalnya: Pembersihan, Perbaikan, Suku Cadang.
            $table->string('category')->nullable();

            $table->string('name');

            // Harga default dalam rupiah.
            $table->unsignedBigInteger('default_price')->default(0);
            $table->unsignedBigInteger('default_cost')->default(0);

            $table->boolean('is_active')->default(true)->index();

            $table->timestamps();

            $table->unique(['item_type', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_catalogs');
    }
};
