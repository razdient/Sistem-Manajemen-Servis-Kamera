<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();

            $table->string('name'); // Nama pelanggan
            $table->string('phone')->nullable(); // Nomor telepon
            $table->text('address')->nullable(); // Alamat pelanggan
            $table->text('notes')->nullable(); // Catatan tambahan

            $table->timestamps();
            $table->softDeletes(); // Mengarsipkan data tanpa menghapus permanen
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
