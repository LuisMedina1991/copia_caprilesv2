<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCheckReceivablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('check_receivables', function (Blueprint $table) {
            $table->id();
            $table->text('description');
            $table->decimal('amount',60,30);
            $table->string('number',45);
            $table->foreignId('bank_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('costumer_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('check_receivables');
    }
}
