<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		$this->call('UserTableSeeder');
		$this->call('NewsTableSeeder');
	}

}

class UserTableSeeder extends Seeder {

    public function run()
    {
        DB::table('users')->delete();

        $users = array(
            array(
                'email'      => 'admin@example.org',
                'password'   => Hash::make('admin'),
                'created_at' => new DateTime,
                'updated_at' => new DateTime,
            ),
        );
        DB::table('users')->insert( $users );
        $this->command->info('User table seeded!');
    	$this->call('NewsTableSeeder');
	}
}