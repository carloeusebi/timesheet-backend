<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Generator;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(Generator $faker): void
    {

        $projects = ['Circool', 'Youvolution', 'Norma', 'Berkem'];

        // an array containing all the activities ids
        $activities = Activity::pluck('id')->toArray();
        // an array containing all the user ids of users with user role
        $users = User::whereRelation('role', 'role', 'user')->pluck('id')->toArray();

        foreach ($projects as $project) {
            // randomize the project activities, every activity has a 50% chance to be selected
            $projectActivities = array_filter($activities, fn () => rand(0, 1));
            //randomize the project users, every user has a 50% chance to be selected
            $projectUsers = array_filter($users, fn () => rand(0, 1));

            $project = Project::create(['name' => $project]);

            $project->activities()->sync($projectActivities);
            $project->users()->sync($projectUsers);
        }
    }
}
