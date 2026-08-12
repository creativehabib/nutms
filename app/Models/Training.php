<?php

namespace App\Models;

use Database\Factories\TrainingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Training extends Model
{
    /** @use HasFactory<TrainingFactory> */
    use HasFactory;

    protected $fillable = [
        'training_type_id', 'allows_repeat', 'title', 'slug', 'description', 'thumbnail', 'start_date', 'end_date',
        'registration_deadline', 'type', 'location_or_link', 'instructor_name',
        'capacity', 'has_certificate', 'status',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'registration_deadline' => 'datetime',
            'has_certificate' => 'boolean',
            'capacity' => 'integer',
            'allows_repeat' => 'boolean',
        ];
    }

    /** @return BelongsToMany<User, $this> */
    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['status', 'approved_by', 'approved_at', 'completed_at', 'certificate_number'])
            ->withTimestamps();
    }

    public function trainingType(): BelongsTo
    {
        return $this->belongsTo(TrainingType::class);
    }

    public function googleCalendarUrl(): string
    {
        return 'https://calendar.google.com/calendar/render?'.http_build_query([
            'action' => 'TEMPLATE',
            'text' => $this->title,
            'dates' => $this->start_date->utc()->format('Ymd\THis\Z').'/'.$this->end_date->utc()->format('Ymd\THis\Z'),
            'details' => $this->description,
            'location' => $this->location_or_link,
        ], encoding_type: PHP_QUERY_RFC3986);
    }
}
