<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
