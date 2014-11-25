<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOpenCloseToExchangesData extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('exchanges_data', function(Blueprint $table)
		{
			$table->decimal('open', 15, 5)
				->nullable();
			$table->decimal('close', 15, 5)
				->nullable();
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
			$table->dropColumn('open');
			$table->dropColumn('close');
		});
	}

}