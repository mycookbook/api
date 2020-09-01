<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFulltextIndexToRecipesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		DB::statement('ALTER TABLE recipes ADD FULLTEXT full(name, description, ingredients, nutritional_detail, summary)');
    }
}
