<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Flashcard extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'question',
        'answer',
        'public',
        'deck_id',
    ];

    protected $casts = [
        'public' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($flashcard) {
            if (empty($flashcard->uuid)) {
                $flashcard->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the deck that owns the flashcard.
     */
    public function deck(): BelongsTo
    {
        return $this->belongsTo(Deck::class);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Scope a query to only include public flashcards.
     */
    public function scopePublic($query)
    {
        return $query->where('public', true);
    }
}
