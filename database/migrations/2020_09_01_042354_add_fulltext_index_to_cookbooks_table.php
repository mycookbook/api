<?php

use Illuminate\Database\Migrations\Migration;

class AddFulltextIndexToCookbooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        DB::statement('ALTER TABLE cookbooks ADD FULLTEXT full(name, description)');
    }
}
