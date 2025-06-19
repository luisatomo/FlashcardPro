<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Deck extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'name',
        'description',
        'public',
        'user_id',
    ];

    protected $casts = [
        'public' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($deck) {
            if (empty($deck->uuid)) {
                $deck->uuid = (string) Str::uuid();
            }
        });

        return;
    }

    /**
     * Get the user that owns the deck.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the flashcards for the deck.
     */
    public function flashcards(): HasMany
    {
        return $this->hasMany(Flashcard::class);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Scope a query to only include public decks.
     */
    public function scopePublic($query)
    {
        return $query->where('public', true);
    }

    /**
     * Get the total number of flashcards in this deck.
     */
    public function getFlashcardCountAttribute(): int
    {
        return $this->flashcards()->count();
    }
}
