<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateCookbooksTable
 */
class CreateCookbooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'cookbooks', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->longText('description');
                $table->string('bookCoverImg');
                $table->integer('user_id')->unsigned();
                $table->timestamps();
            }
        );

        Schema::table(
            'cookbooks', function ($table) {
                $table
                    ->foreign('user_id')
                    ->references('id')
                    ->on('users')
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
        Schema::dropIfExists('cookbooks');
    }
}
