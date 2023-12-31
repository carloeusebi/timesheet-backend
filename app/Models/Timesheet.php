<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timesheet extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'project_id', 'activity_id', 'date', 'hours', 'description'];

    protected $with = ['user', 'project', 'activity'];

    static function labels()
    {
        return [
            'userId' => 'Impiegato',
            'projectId' => 'Progetto',
            'activityId' => 'Attività',
            'date' => 'Data',
            'hours' => 'Ore',
            'description' => 'Descrizione'
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class)->withTrashed();
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class)->withTrashed();
    }
}
