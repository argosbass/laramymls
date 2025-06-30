<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('property_photos', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
            $table->foreignId('property_id')->constrained('properties')->onDelete('cascade');
            $table->string('photo_url', 1000)->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->text('photo_alt')->nullable()->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->text('photo_title')->nullable()->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_photos');
    }
};
