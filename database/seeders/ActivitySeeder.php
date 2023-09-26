<?php

namespace Database\Seeders;

use App\Models\Activity;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activities = ['Design', 'Development', 'Testing', 'Documentation'];

        foreach ($activities as $activity) {
            Activity::create(['name' => $activity]);
        }
    }
}
