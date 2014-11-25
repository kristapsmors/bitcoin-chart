<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToExchangesData extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('exchanges_data', function(Blueprint $table)
		{
			$table->index(['market', 'from_currency', 'to_currency', 'datetime']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('exchanges_data', function(Blueprint $table)
		{
			$table->dropIndex(['market', 'from_currency', 'to_currency', 'datetime']);
		});
	}

}