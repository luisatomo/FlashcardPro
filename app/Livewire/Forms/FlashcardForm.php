<?php

namespace App\Livewire\Forms;

use App\Models\Deck;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use Livewire\Form;

class FlashcardForm extends Form
{
    #[Validate('required|string|min:3|max:255')]
    public string $question = '';

    #[Validate('required|string|min:3')]
    public string $answer = '';

    #[Validate('boolean')]
    public bool $public = false;

    public ?int $editingId = null;

    public ?Deck $deck = null;

    /**
     * Additional validation rules that can't be expressed in attributes
     */
    public function rules(): array
    {
        return [
            'question' => [
                'required',
                'string',
                'min:3',
                'max:100',
                'regex:/^[\w\s\-_.,!?()]+$/u', // Only allow safe characters
                function ($attribute, $value, $fail) {
                    if ($this->detectMaliciousInput($value)) {
                        $fail('The ' . $attribute . ' contains invalid content.');
                    }
                },
            ],
            'answer' => [
                'required',
                'string',
                'min:3',
                'regex:/^[\w\s\-_.,!?()]+$/u', // Only allow safe characters
                function ($attribute, $value, $fail) {
                    if ($this->detectMaliciousInput($value)) {
                        $fail('The ' . $attribute . ' contains invalid content.');
                    }
                },
            ],
            'public' => 'boolean',
        ];
    }

    /**
     * Custom validation messages
     */
    public function messages(): array
    {
        return [
            'question.required' => 'Please enter a question.',
            'question.min' => 'Question must be at least 3 characters.',
            'question.max' => 'Question cannot exceed 255 characters.',
            'question.regex' => 'Question contains invalid characters. Only letters, numbers, spaces, and basic punctuation are allowed.',
            'answer.required' => 'Please enter an answer.',
            'answer.min' => 'Answer must be at least 3 characters.',
            'answer.regex' => 'Answer contains invalid characters. Only letters, numbers, spaces, and basic punctuation are allowed.',
        ];
    }

    /**
     * Prepare data for validation by sanitizing inputs
     */
    protected function prepareForValidation($attributes): array
    {
        if (isset($attributes['question'])) {
            $attributes['question'] = $this->sanitizeValue($attributes['question']);
        }

        if (isset($attributes['answer'])) {
            $attributes['answer'] = $this->sanitizeValue($attributes['answer']);
        }

        if (isset($attributes['public'])) {
            $attributes['public'] = (bool) $attributes['public'];
        }

        return $attributes;
    }

    /**
     * Sanitize the Card inputs
     */
    private function sanitizeValue(string $value): string
    {
        return Str::of($value)
            ->trim()                           // Remove leading/trailing spaces
            ->stripTags()                      // Remove HTML tags
            ->replaceMatches('/\s+/', ' ')     // Replace multiple spaces with single space
            ->replaceMatches('/[<>"\'`]/', '') // Remove dangerous characters
            ->limit(100)                       // Limit length
            ->toString();
    }

    /**
     * Detect potentially malicious input
     */
    private function detectMaliciousInput(string $input): bool
    {
        $patterns = [
            '/script|javascript|vbscript/i',                    // Script injection
            '/(union|select|insert|update|delete|drop)/i',      // SQL injection
            '/<iframe|<object|<embed|<form/i',                  // Dangerous HTML
            '/on\w+\s*=/i',                                     // Event handlers
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get sanitized data ready for database operations
     */
    public function getSanitizedData(): array
    {
        return [
            'question' => $this->sanitizeValue($this->question),
            'answer' => $this->sanitizeValue($this->answer),
            'public' => (bool) $this->public,
            'deck' => $this->deck,
        ];
    }

    /**
     * Reset form to initial state
     */
    public function reset(...$properties): void
    {
        if (empty($properties)) {
            $properties = ['question', 'answer', 'public', 'editingId'];
        }

        parent::reset(...$properties);
    }

    /**
     * Fill the form with card data for editing
     */
    public function fill($values): void
    {
        $this->question = $values['question'] ?? '';
        $this->answer = $values['answer'] ?? '';
        $this->public = (bool) ($values['public'] ?? false);
        $this->deck = $values['deck'] ?? null;
        $this->editingId = $values['id'] ?? null;
    }

    /**
     * Set the deck for this form
     */
    public function setDeck(Deck $deck): void
    {
        $this->deck = $deck;
    }


    /**
     * Check if form is in editing mode
     */
    public function isEditing(): bool
    {
        return !is_null($this->editingId);
    }
}
