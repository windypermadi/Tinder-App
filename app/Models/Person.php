<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Person extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'people';

    protected $fillable = [
        'name',
        'age',
        'pictures',
        'location',
        'likes_count',
        'email_sent',
    ];

    protected $casts = [
        'pictures' => 'array',
        'age' => 'integer',
        'likes_count' => 'integer',
        'email_sent' => 'boolean',
    ];

    /**
     * Get interactions where this person liked others
     */
    public function likesGiven()
    {
        return $this->hasMany(Interaction::class, 'from_person_id')
            ->where('type', 'like');
    }

    /**
     * Get interactions where this person was liked by others
     */
    public function likesReceived()
    {
        return $this->hasMany(Interaction::class, 'to_person_id')
            ->where('type', 'like');
    }

    /**
     * Get all interactions from this person
     */
    public function interactionsGiven()
    {
        return $this->hasMany(Interaction::class, 'from_person_id');
    }

    /**
     * Get all interactions received by this person
     */
    public function interactionsReceived()
    {
        return $this->hasMany(Interaction::class, 'to_person_id');
    }

    /**
     * Get people who liked this person
     */
    public function likedBy()
    {
        return $this->belongsToMany(Person::class, 'interactions', 'to_person_id', 'from_person_id')
            ->wherePivot('type', 'like')
            ->withTimestamps();
    }

    /**
     * Get people this person liked
     */
    public function liked()
    {
        return $this->belongsToMany(Person::class, 'interactions', 'from_person_id', 'to_person_id')
            ->wherePivot('type', 'like')
            ->withTimestamps();
    }
}

