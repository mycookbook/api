<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecipeVariationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recipe_variations', function (Blueprint $table) {
            $table->increments('id');

			$table->string('name');
			$table->string('ingredients');
			$table->string('imgUrl');
			$table->longText('description');

			$table->integer('recipe_id')->unsigned();

            $table->timestamps();
        });

		Schema::table(
			'recipe_variations', function ($table) {
			$table
				->foreign('recipe_id')
				->references('id')
				->on('recipes')
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
        Schema::dropIfExists('recipe_variations');
    }
}
