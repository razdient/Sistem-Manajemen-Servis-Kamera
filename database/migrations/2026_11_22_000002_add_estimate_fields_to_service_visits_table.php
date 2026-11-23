<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_visits', function (Blueprint $table) {
            $table->string('estimate_status')->default('not_started')->index();
            $table->timestamp('estimated_at')->nullable();
            $table->timestamp('decision_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('service_visits', function (Blueprint $table) {
            $table->dropColumn([
                'estimate_status',
                'estimated_at',
                'decision_at',
            ]);
        });
    }
};
