<?php

use Illuminate\Database\Seeder;

use Carbon\Carbon;

class BlacklistedKeywordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('keyword_blacklists')->insert([
            'keyword' => 'sex',
            'created_at' => Carbon::now(),
            'updated_at'=> Carbon::now(),
        ]);

        DB::table('keyword_blacklists')->insert([
            'keyword' => 'nude',
            'created_at' => Carbon::now(),
            'updated_at'=> Carbon::now(),
        ]);

        DB::table('keyword_blacklists')->insert([
            'keyword' => 'happy',
            'created_at' => Carbon::now(),
            'updated_at'=> Carbon::now(),
        ]);
    }
}
