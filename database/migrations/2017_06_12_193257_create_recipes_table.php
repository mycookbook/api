<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class Create Recipes Table
 */
class CreateRecipesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'recipes', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('ingredients');
                $table->string('imgUrl');
                $table->longText('description');

                $table->integer('user_id')->unsigned();
                $table->integer('cookbook_id')->unsigned();
                $table->integer('nationality')->nullable();

                $table->timestamps();
            }
        );

        Schema::table(
            'recipes', function ($table) {
                $table
                    ->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
            }
        );

        Schema::table(
            'recipes', function ($table) {
                $table
                    ->foreign('cookbook_id')
                    ->references('id')
                    ->on('cookbooks')
                    ->onDelete('cascade');
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recipes');
    }
}
