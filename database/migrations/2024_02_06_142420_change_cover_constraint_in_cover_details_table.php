<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeCoverConstraintInCoverDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cover_details', function (Blueprint $table) {
            $table->unsignedBigInteger('cover_id')->nullable()->change();

            $table->dropForeign(['cover_id']);
            $table->foreign('cover_id')->references('id')->on('covers')->cascadeOnUpdate()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cover_details', function (Blueprint $table) {
            $table->unsignedBigInteger('cover_id')->change();

            $table->dropForeign(['cover_id']);
            $table->foreign('cover_id')->references('id')->on('covers')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }
}
