<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfficeProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('office_product', function (Blueprint $table) {
            $table->id();
            $table->integer('stock')->nullable();
            $table->integer('alerts')->nullable();
            $table->foreignId('office_id')->constrained()->onUpdate('cascade')->onDelete('cascade');    //relacion  de llave foranea con tabla categories
            $table->foreignId('product_id')->constrained()->onUpdate('cascade')->onDelete('cascade'); //relacion  de llave foranea con tabla subcategories
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('office_product');
    }
}
