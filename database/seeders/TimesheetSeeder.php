<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Project;
use App\Models\Timesheet;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Generator;

class TimesheetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(Generator $faker): void
    {
        $NUMBER_OF_TIMESHEETS = 100;

        $i = 0;
        while ($i < $NUMBER_OF_TIMESHEETS) {

            $user = User::inRandomOrder()->first();

            $project = Project::whereRelation('users', 'user_id', $user->id)->inRandomOrder()->first();
            if (!$project) continue; // if a user has no project assigned;

            $activity = Activity::whereRelation('projects', 'project_id', $project->id)->inRandomOrder()->first();
            if (!$activity) continue; //if a project has no activity assigned;

            $activity_start = $faker->dateTimeBetween('-2 years');

            Timesheet::create([
                'user_id' => $user->id,
                'project_id' => $project->id,
                'activity_id' => $activity->id,
                'activity_start' => $activity_start,
                'activity_end' => Carbon::parse($activity_start)->addMinutes(rand(60, 60 * 8)),
                'description' => $faker->text(500),
            ]);

            $i++;
        }
    }
}
