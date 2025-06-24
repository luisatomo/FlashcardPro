<?php

namespace App\Livewire\Forms;

use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use Livewire\Form;

class DeckForm extends Form
{
    #[Validate('required|string|min:3|max:100')]
    public string $name = '';

    #[Validate('boolean')]
    public bool $public = false;

    public ?int $editingId = null;

    /**
     * Additional validation rules that can't be expressed in attributes
     */
    public function rules(): array
    {
        return [
            'name' => [
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
            'public' => 'boolean',
        ];
    }

    /**
     * Custom validation messages
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Please enter a deck name.',
            'name.min' => 'Deck name must be at least 3 characters.',
            'name.max' => 'Deck name cannot exceed 100 characters.',
            'name.regex' => 'Deck name contains invalid characters. Only letters, numbers, spaces, and basic punctuation are allowed.',
        ];
    }

    /**
     * Prepare data for validation by sanitizing inputs
     */
    protected function prepareForValidation($attributes): array
    {
        if (isset($attributes['name'])) {
            $attributes['name'] = $this->sanitizeName($attributes['name']);
        }

        if (isset($attributes['public'])) {
            $attributes['public'] = (bool) $attributes['public'];
        }

        return $attributes;
    }

    /**
     * Sanitize the deck name
     */
    private function sanitizeName(string $name): string
    {
        return Str::of($name)
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
            'name' => $this->sanitizeName($this->name),
            'public' => (bool) $this->public,
        ];
    }

    /**
     * Reset form to initial state
     */
    public function reset(...$properties): void
    {
        if (empty($properties)) {
            $properties = ['name', 'public', 'editingId'];
        }

        parent::reset(...$properties);
    }

    /**
     * Fill form with deck data for editing
     */
    public function fill($values): void
    {
        $this->name = $values['name'] ?? '';
        $this->public = (bool) ($values['public'] ?? false);
        $this->editingId = $values['id'] ?? null;
    }

    /**
     * Check if form is in editing mode
     */
    public function isEditing(): bool
    {
        return !is_null($this->editingId);
    }
}
