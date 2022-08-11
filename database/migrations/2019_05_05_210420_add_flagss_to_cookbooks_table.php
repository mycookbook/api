<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFlagssToCookbooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cookbooks', function (Blueprint $table) {
            $table->integer('flag_id')->nullable();
        });

//        Schema::table(
//            'cookbooks', function ($table) {
//            $table
//                ->foreign('flag_id')
//                ->references('id')
//                ->on('flags')
//                ->onDelete('cascade');
//            }
//        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cookbooks', function (Blueprint $table) {
            $table->dropColumn('flag_id');
        });
    }
}
