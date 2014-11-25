<?php

use Illuminate\Database\Migrations\Migration;

class AddCurrencyToExchangeData extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('exchanges_data', function ($table)
		{
			$table->string('currency', 10)->default('USD');
			$table->index('currency');
		});

		Schema::table('exchanges_last_price', function ($table)
		{
			$table->string('currency', 10)->default('USD');
			$table->index('currency');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('exchanges_data', function ($table)
		{
			$table->dropColumn('currency');
			$table->dropIndex('currency');
		});

		Schema::table('exchanges_last_price', function ($table)
		{
			$table->dropColumn('currency');
			$table->dropIndex('currency');
		});
	}
}
