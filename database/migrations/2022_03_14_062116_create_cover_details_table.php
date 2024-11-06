<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoverDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cover_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cover_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->enum('type',['balance_mensual','mercaderia','efectivo','creditos','depositos','por_pagar','utilidad_diaria','gasto_diario','facturas_mensual']);
            $table->decimal('previus_day_balance',10,2);
            $table->decimal('ingress',10,2);
            $table->decimal('egress',10,2);
            $table->decimal('actual_balance',10,2);
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
        Schema::dropIfExists('cover_details');
    }
}
