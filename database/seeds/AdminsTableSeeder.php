<?php

use Illuminate\Database\Seeder;

class AdminsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('admins')->delete();
        $adminsRecords = [
        	['id'=>1,'name'=>'admin','type'=>'admin','mobile'=>'017888','email'=>'admin@admin.com',
        	 'password'=>'$2y$10$doUSvOVP.Tovn8I4mklLrO.Kv7IbeoxWWGQZ.ENGI0o3aK4USO8e.','image'=>'','status'=>1
        	],
        ];
        
        DB::table('admins')->insert($adminsRecords);
        /*
        foreach ($adminsRecords as $key => $record) {
            \App\Admin::create($record);        	
        }
        */
    }
}
