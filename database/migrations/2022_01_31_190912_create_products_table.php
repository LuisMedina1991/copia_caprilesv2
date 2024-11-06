<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('description',45);
            $table->string('code',45)->unique();
            $table->string('brand',45)->nullable();
            $table->string('ring',45)->nullable();
            $table->string('threshing',45)->nullable();
            $table->string('tarp',45)->nullable();
            $table->decimal('cost',60,30);
            $table->decimal('price',60,30);
            $table->unsignedBigInteger('category_subcategory_id');
            $table->foreign('category_subcategory_id')->references('id')->on('category_subcategory')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('state_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('products');
    }
}
