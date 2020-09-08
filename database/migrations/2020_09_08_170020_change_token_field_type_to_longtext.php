<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class ChangeTokenFieldTypeToLongtext extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		DB::raw('ALTER TABLE email_verifications MODIFY token longtext');
    }
}
