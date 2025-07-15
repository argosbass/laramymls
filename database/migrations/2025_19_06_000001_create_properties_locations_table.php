<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('property_locations', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED
            $table->string('location_name', 255);
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('property_locations')
                ->nullOnDelete();

            $table->unsignedInteger('_lft')->nullable();
            $table->unsignedInteger('_rgt')->nullable();
            $table->unsignedInteger('depth')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_locations');
    }
};
