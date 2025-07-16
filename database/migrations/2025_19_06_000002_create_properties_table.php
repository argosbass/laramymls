<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
            $table->unsignedInteger('nid')->unique()->nullable();

            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('slug')->unique();
            $table->tinyInteger('published')->nullable();
            $table->string('property_title', 500)->nullable();
            $table->date('property_added_date')->nullable();
            $table->float('property_bathrooms')->nullable();
            $table->float('property_bathrooms_inner')->nullable();
            $table->float('property_bedrooms')->nullable();
            $table->longText('property_body')->nullable();
            $table->float('property_building_size_m2')->nullable();
            $table->float('property_building_size_area_quantity')->nullable();
            $table->enum('property_building_size_area_unit', ['sqm', 'sqft'])->nullable();
            $table->double('property_geolocation_lat')->nullable();
            $table->double('property_geolocation_lng')->nullable();
            $table->double('property_geolocation_lat_sin')->nullable();
            $table->double('property_geolocation_lat_cos')->nullable();
            $table->double('property_geolocation_lng_rad')->nullable();
            $table->double('property_hoa_fee')->nullable();
            $table->float('property_lot_size_area_quantity')->nullable();
            $table->enum('property_lot_size_area_unit', ['sqm', 'sqft'])->nullable();
            $table->float('property_lot_size_m2')->nullable();
            $table->float('property_no_of_floors')->nullable();
            $table->longText('property_notes_to_agents')->nullable();
            $table->float('property_on_floor_no')->nullable();
            $table->float('property_osnid')->nullable();
            $table->float('property_price')->nullable();


            $table->foreignId('property_status_id')->nullable()->constrained('property_status')->nullOnDelete();
            $table->foreignId('property_type_id')->nullable()->constrained('property_types')->nullOnDelete();
            $table->foreignId('property_location_id')->nullable()->constrained('property_locations')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('property_video', 500)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
