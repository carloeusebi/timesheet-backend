<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Project;
use App\Models\Timesheet;
use App\Models\User;
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

            Timesheet::create([
                'user_id' => $user->id,
                'project_id' => $project->id,
                'activity_id' => $activity->id,
                'date' => $faker->date(),
                'hours' => rand(1, 8),
                'description' => $faker->text(500),
            ]);

            $i++;
        }
    }
}
