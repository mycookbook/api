<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCategoryToCookbook extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cookbooks', function (Blueprint $table) {
            $table->integer('category_id')->nullable();
        });

//        Schema::table(
//            'cookbooks', function ($table) {
//            $table
//                ->foreign('category_id')
//                ->references('id')
//                ->on('categories')
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
            $table->dropColumn('category_id');
        });
    }
}
