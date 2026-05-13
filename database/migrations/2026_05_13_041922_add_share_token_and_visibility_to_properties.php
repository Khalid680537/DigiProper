<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->string('share_token', 32)->nullable()->unique()->after('id');
            $table->boolean('share_financials')->default(false)->after('is_data_complete');
            $table->boolean('share_contacts')->default(false)->after('share_financials');
            $table->boolean('share_keys_location')->default(false)->after('share_contacts');
            $table->boolean('share_extra_notes')->default(false)->after('share_keys_location');
            $table->boolean('share_title_holder')->default(false)->after('share_extra_notes');
        });

        DB::table('properties')->whereNull('share_token')->orderBy('id')->each(function ($property) {
            DB::table('properties')
                ->where('id', $property->id)
                ->update(['share_token' => Str::random(32)]);
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropUnique(['share_token']);
            $table->dropColumn([
                'share_token',
                'share_financials',
                'share_contacts',
                'share_keys_location',
                'share_extra_notes',
                'share_title_holder',
            ]);
        });
    }
};
