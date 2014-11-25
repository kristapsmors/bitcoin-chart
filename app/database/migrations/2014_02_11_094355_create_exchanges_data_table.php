<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExchangesDataTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
	    Schema::create('exchanges_data', function($table)
	    {
	    	$table->string('market');
	        $table->decimal('low', 15, 5);
	        $table->decimal('high',15, 5);
	        $table->decimal('avg', 15, 5);
	        $table->decimal('volume', 40, 5);
	        $table->decimal('volume_cur', 40, 5);
	        $table->timestamps();
	    });
	}

	public function down()
	{
	    Schema::drop('exchanges_data');
	}

}
