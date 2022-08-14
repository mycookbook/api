<?php

use Illuminate\Database\Migrations\Migration;

class AddFulltextIndexToRecipesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        DB::statement('ALTER TABLE recipes ADD FULLTEXT full(name, description, ingredients, nutritional_detail, summary)');
    }
}
