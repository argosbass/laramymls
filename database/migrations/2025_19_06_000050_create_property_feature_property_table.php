<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('property_feature_property', function (Blueprint $table) {
           // $table->unsignedBigInteger('property_id');
           // $table->unsignedBigInteger('feature_id');

            $table->foreignId('property_id')->constrained('properties')->onDelete('cascade');
            $table->foreignId('feature_id')->constrained('property_features')->onDelete('cascade');
           $table->primary(['property_id', 'feature_id']);

         //   $table->timestamps();

            // $table->primary(['property_id', 'feature_id']);

            // $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
            // $table->foreign('feature_id')->references('id')->on('property_features')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_feature_property');
    }
};
