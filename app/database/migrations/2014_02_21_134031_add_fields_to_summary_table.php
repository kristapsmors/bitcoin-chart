<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToSummaryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('exchanges_summary', function (Blueprint $table) {
            $table->integer('id', true);
			$table->string('market', 50);
			$table->string('from_currency', 10);
			$table->string('to_currency', 10);
			$table->string('type', 10);
			$table->decimal('last', 20, 8);
			$table->decimal('low', 20, 8);
			$table->decimal('open', 20, 8);
			$table->decimal('close', 20, 8);
			$table->decimal('high', 20, 8);
			$table->decimal('avg', 20, 8);
			$table->decimal('volume', 30, 8);
			$table->decimal('volume_cur', 30, 8);
			$table->timestamps();

			$table->index(['market', 'from_currency', 'to_currency', 'type']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('exchanges_summary');
	}

}