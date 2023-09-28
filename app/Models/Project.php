<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    protected $with = ['activities'];

    public function activities()
    {
        return $this->belongsToMany(Activity::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
