<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAnotherCurrencyField extends Migration {

	public function up()
	{
		Schema::table('exchanges_data', function (Blueprint $table)
		{
			$table->string('from_currency', 10)
				->default('BTC')
				->index();
			$table->renameColumn('currency', 'to_currency');
		});

		Schema::table('exchanges_last_price', function (Blueprint $table)
		{
			$table->string('from_currency', 10)
				->default('BTC')
				->index();
			$table->renameColumn('currency', 'to_currency');
		});
	}

	public function down()
	{
		Schema::table('exchanges_data', function (Blueprint $table)
		{
			$table->renameColumn('to_currency', 'currency');
			$table->dropColumn('from_currency');
		});

		Schema::table('exchanges_last_price', function (Blueprint $table)
		{
			$table->renameColumn('to_currency', 'currency');
			$table->dropColumn('from_currency');
		});
	}
}
