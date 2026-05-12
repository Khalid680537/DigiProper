<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('title_holder')->nullable();
            $table->string('rera_number')->nullable();

            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('pincode', 6)->nullable();

            $table->string('tenure')->nullable();
            $table->string('tenure_authority')->nullable();
            $table->string('occupancy_status')->nullable();
            $table->string('tenant_or_occupant')->nullable();

            $table->string('construction')->nullable();
            $table->decimal('area_value', 12, 2)->nullable();
            $table->string('area_unit')->nullable();

            $table->decimal('imputed_value_inr', 15, 2)->nullable();
            $table->decimal('rent_yearly_inr', 15, 2)->nullable();
            $table->decimal('yield_percent', 5, 2)->nullable();

            $table->json('contacts')->nullable();
            $table->string('keys_location')->nullable();
            $table->text('extra_notes')->nullable();
            $table->boolean('is_data_complete')->default(false);

            $table->timestamps();
            $table->softDeletes();
            $table->auditUsers();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
