<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExchangesLastPriceTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
	    Schema::create('exchanges_last_price', function($table)
	    {
	    	$table->increments('id');
	    	$table->string('market');
	        $table->decimal('last', 9, 2);
	        $table->timestamps();
	    });
	}

	public function down()
	{
	    Schema::drop('exchanges_last_price');
	}

}
