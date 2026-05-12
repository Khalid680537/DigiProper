<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();

            // PII fields are stored encrypted at rest (DPDP Act 2023).
            // Ciphertext is opaque and longer than plaintext, so use text.
            $table->text('name')->nullable();
            $table->text('phone')->nullable();
            $table->text('notes')->nullable();
            $table->string('role')->nullable();

            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_contacts');
    }
};
