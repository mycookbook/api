<?php

use Illuminate\Database\Migrations\Migration;

class AddFulltextIndexToRecipeVariationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE recipe_variations ADD FULLTEXT full(name, description, ingredients)');
    }
}
