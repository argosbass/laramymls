<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRealEstateCompaniesTable extends Migration
{
    public function up()
    {
        Schema::create('real_estate_companies', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('nid')->unique()->nullable();
            $table->tinyInteger('published')->nullable();
            $table->string('company_title', 255)->nullable();
            $table->string('company_city_town', 100)->nullable();
            $table->string('company_name', 255)->nullable();
            $table->string('company_main_contact', 255)->nullable();
            $table->string('company_main_telephone', 100)->nullable();
            $table->longText('company_notes_to_agents')->nullable();
            $table->string('company_post_code', 50)->nullable();
            $table->string('company_province', 100)->nullable();
            $table->string('company_street_address_1', 255)->nullable();
            $table->string('company_street_address_2', 255)->nullable();
            $table->string('company_website_url', 255)->nullable();
            $table->string('company_website_text', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('real_estate_companies');
    }
}
