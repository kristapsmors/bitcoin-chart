<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTransactionsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('transactions', function (Blueprint $table)
		{
			$table->string('id', 50);
			$table->string('market', 50);
			$table->string('from_currency', 5);
			$table->string('to_currency', 5);
			$table->datetime('made_at');
			$table->decimal('in_currency_buy', 30, 8);
			$table->decimal('in_currency_sell', 30, 8);
			$table->decimal('amount_buy', 30, 8);
			$table->decimal('amount_sell', 30, 8);
			$table->integer('count_buy');
			$table->integer('count_sell');
			$table->string('last_transaction_id');
			$table->timestamps();

			$table->index(['market', 'from_currency', 'to_currency', 'made_at', 'last_transaction_id'], 'index_0');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('transactions');
	}

}
