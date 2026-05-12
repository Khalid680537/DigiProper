<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            // The global owner scope filters every Property query by created_by,
            // and SoftDeletes filters by deleted_at on every read. Index both
            // to keep query plans cheap as the dataset grows.
            $table->index('deleted_at');
            $table->index(['created_by', 'deleted_at']);
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropIndex(['deleted_at']);
            $table->dropIndex(['created_by', 'deleted_at']);
        });
    }
};
