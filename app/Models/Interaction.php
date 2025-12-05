<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_person_id',
        'to_person_id',
        'type',
    ];

    protected $casts = [
        'from_person_id' => 'integer',
        'to_person_id' => 'integer',
    ];

    /**
     * Get the person who initiated the interaction
     */
    public function fromPerson()
    {
        return $this->belongsTo(Person::class, 'from_person_id');
    }

    /**
     * Get the person who received the interaction
     */
    public function toPerson()
    {
        return $this->belongsTo(Person::class, 'to_person_id');
    }
}

