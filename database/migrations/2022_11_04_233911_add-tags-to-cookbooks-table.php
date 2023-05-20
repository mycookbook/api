<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTagsToCookbooksTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('cookbooks', 'tags')) {
            Schema::table('cookbooks', function (Blueprint $table) {
                $table->text('tags')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cookbooks', function (Blueprint $table) {
            $table->dropIfExists('tags');
        });
    }
};
