<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertySoldReferencesTable extends Migration
{
    public function up()
    {
        Schema::create('property_sold_references', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->onDelete('cascade');
            $table->unsignedInteger('nid');
            $table->date('sold_reference_date')->nullable();
            $table->float('sold_reference_price')->nullable();
            $table->longText('sold_reference_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('property_sold_references');
    }
}
