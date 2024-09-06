<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShopIdToExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('expenses', function (Blueprint $table) {
            // Add the shop_id column, defaulting to 1, and setting up the foreign key relationship
            $table->foreignId('shop_id')
                  ->default(1)
                  ->constrained('shops')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('expenses', function (Blueprint $table) {
            // Drop the foreign key and the column
            $table->dropForeign(['shop_id']);
            $table->dropColumn('shop_id');
        });
    }
}
