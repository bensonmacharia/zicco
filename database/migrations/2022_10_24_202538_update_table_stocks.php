<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTableStocks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->unsignedBigInteger('order_id')->default('1');
            $table->integer('batch');
            $table->tinyInteger('soldout')->default('0');
            $table->integer('spoilt')->default('0');
            $table->renameColumn('cost', 'pcost');
            $table->double('ccost');
            $table->double('tcost');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->dropColumn(['order_id']);
            $table->dropColumn(['batch']);
            $table->double('cost');
            $table->dropColumn(['spoilt']);
            $table->dropColumn(['pcost']);
            $table->dropColumn(['ccost']);
            $table->dropColumn(['tcost']);
        });
    }
}
