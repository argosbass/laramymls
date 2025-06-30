<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertyListingCompetitorsTable extends Migration
{
    public function up()
    {
        Schema::create('property_listing_competitors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->onDelete('cascade');
            $table->unsignedInteger('nid');
            $table->string('competitor_listing_agent', 255)->nullable();
            $table->string('competitor_company_name', 255)->nullable();
            $table->text('competitor_property_link')->nullable();
            $table->float('competitor_list_price')->nullable();
            $table->longText('competitor_notes')->nullable();
            $table->foreignId('real_estate_company_id')->nullable()->constrained('real_estate_companies')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('property_listing_competitors');
    }
}
