<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_visits', function (Blueprint $table) {
            $table->timestamp('ready_at')->nullable()->after('decision_at');
            $table->timestamp('warranty_started_at')->nullable()->after('ready_at');
            $table->timestamp('warranty_ended_at')->nullable()->after('warranty_started_at');
        });
    }

    public function down(): void
    {
        Schema::table('service_visits', function (Blueprint $table) {
            $table->dropColumn([
                'ready_at',
                'warranty_started_at',
                'warranty_ended_at',
            ]);
        });
    }
};
