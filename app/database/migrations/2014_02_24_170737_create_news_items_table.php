<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewsItemsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::dropIfExists('news');
		Schema::create('news_items', function(Blueprint $table) {
			$table->increments('id');
			$table->string('title');
			$table->string('link');
			$table->string('currency');
			$table->dateTime('datetime');
			$table->decimal('price', 20, 8);
			$table->decimal('price_24h', 20, 8);
			$table->decimal('price_3d', 20, 8);
			$table->decimal('price_week', 20, 8);
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('news_items');
	}

}
