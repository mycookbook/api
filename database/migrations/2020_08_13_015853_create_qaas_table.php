<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQaasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qaas', function (Blueprint $table) {
            $table->increments('id');
			$table->string('phone');
			$table->string('question');
			$table->dateTime('question_asked');
			$table->string('answer');
			$table->dateTime('answer_given');
			$table->integer('author_id')->unsigned();
			$table->integer('recipe_id')->unsigned();
			$table->integer('variety_id')->unsigned();
        });


		Schema::table(
			'qaas', function ($table) {
				$table
					->foreign('author_id')
					->references('id')
					->on('users')
					->onDelete('cascade');
			}
		);

		Schema::table(
			'qaas', function ($table) {
				$table
					->foreign('recipe_id')
					->references('id')
					->on('recipes')
					->onDelete('cascade');
			}
		);

		Schema::table(
			'qaas', function ($table) {
				$table
					->foreign('variety_id')
					->references('id')
					->on('recipe_variations')
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
        Schema::dropIfExists('qaas');
    }
}
