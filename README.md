# FlashcardPro

This application allows users to create, manage, and study flashcards.  
The primary interface is built using Laravel and Laravel Livewire.

## Requirements

- Docker installed

## Getting Started

1. **Clone the repository**

   ```bash
   git clone git@github.com:luisatomo/FlashcardPro.git
   cd FlashcardPro
   ```

2. **Start Sail**

   From the root of the project:

   ```bash
   ./vendor/bin/sail up -d
   ```

3. **Install PHP dependencies**

   ```bash
   ./vendor/bin/sail composer install
   ```

4. **Run database migrations**

   ```bash
   ./vendor/bin/sail artisan migrate
   ```

5. **Run lint scripts**

    - To check code style with PSR-12:

      ```bash
      ./vendor/bin/sail composer lint
      ```

    - To automatically fix fixable style issues:

      ```bash
      ./vendor/bin/sail composer lint-fix
      ```

> More documentation and setup steps will be added soon.
