<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserContactDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
	public function up()
	{
		Schema::create('user_contact_details', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('user_id')->unsigned();
			$table->string('visibility')->default('public');
			$table->string('facebook')->nullable();
			$table->string('twitter')->nullable();
			$table->string('instagram')->nullable();
			$table->string('office_address')->nullable();
			$table->string('phone')->nullable();
			$table->string('calendly')->nullable();
			$table->string('skype')->nullable();
			$table->string('website')->nullable();
			$table->timestamps();
		});

		Schema::table(
			'user_contact_details', function ($table) {
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
        Schema::dropIfExists('user_contact_details');
    }
}
