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
        // $NUMBER_OF_TIMESHEETS = 100;


        // $i = 0;
        // while ($i < $NUMBER_OF_TIMESHEETS) {

        //     $user = User::inRandomOrder()->first();

        //     $project = Project::whereRelation('users', 'user_id', $user->id)->inRandomOrder()->first();
        //     if (!$project) continue; // if a user has no project assigned;

        //     $activity = Activity::whereRelation('projects', 'project_id', $project->id)->inRandomOrder()->first();
        //     if (!$activity) continue; //if a project has no activity assigned;

        //     Timesheet::create([
        //         'user_id' => $user->id,
        //         'project_id' => $project->id,
        //         'activity_id' => $activity->id,
        //         'date' => $faker->dateTimeBetween('-5 years'),
        //         'hours' => rand(1, 16) / 2,
        //         'description' => $faker->text(500),
        //     ]);

        //     $i++;
        // }

        $users = User::all();

        foreach ($users as $user) {
            $startDate = Carbon::parse('2023-08-01');
            $endDate = Carbon::yesterday();

            if ($user->projects->count() === 0) continue;

            while ($startDate->lte($endDate)) {

                $startDate->addDay();

                $project = $user->projects()->inRandomOrder()->first();

                if ($project->activities->count() === 0) continue;
                $activity = $project->activities()->inRandomOrder()->first();

                Timesheet::create([
                    'user_id' => $user->id,
                    'project_id' => $project->id,
                    'activity_id' => $activity->id,
                    'description' => $faker->text(500),
                    'date' => $startDate,
                    'hours' => 8,
                ]);
            }
        }
    }
}
