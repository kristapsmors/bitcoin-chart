<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDateToExchangeData extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('exchanges_data', function (Blueprint $table)
		{
			$table->datetime('datetime');
			$table->index(['from_currency', 'to_currency', 'datetime']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('exchanges_data', function (Blueprint $table)
		{
			$table->dropColumn('datetime');
		});
	}

}
