<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ChangeLastTransactionId extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('transactions', function (Blueprint $table)
		{
			$table->renameColumn('last_transaction_id', 'old_txid');
		});
		Schema::table('transactions', function (Blueprint $table)
		{
			$table->bigInteger('last_transaction_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('transactions', function (Blueprint $table)
		{
			$table->dropColumn('last_transaction_id');
		});
		Schema::table('transactions', function (Blueprint $table)
		{
			$table->renameColumn('old_txid', 'last_transaction_id');
		});
	}

}