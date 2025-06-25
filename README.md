# FlashcardPro

This application allows users to create, manage, and study flashcards.  
The primary interface is built using Laravel and Laravel Livewire.

## Developer Information

**Name:** Luis Mendoza  
**Email:** luis@atomoweb.com

## PHP & Laravel Versions
PHP: 8.4  
Laravel: v12 latest version

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
   
5. **Run seeds and maybe refresh migrations**
      ```bash
      ./vendor/bin/sail artisan migrate:fresh --seed
      ```
   
6. **Run tests**
      ```bash
      ./vendor/bin/sail artisan test
      ```
      

7. **Run lint scripts**

    - To check code style with PSR-12:

      ```bash
      ./vendor/bin/sail composer lint
      ```

    - To automatically fix fixable style issues:

      ```bash
      ./vendor/bin/sail composer lint-fix
      ```
8. ./vendor/bin/sail npm run dev or ./vendor/bin/sail npm run build
> For the architectural decisions, I initially drew from my experience working on previous Domain-Driven Design (DDD) Laravel projects. However, given the smaller scope of this project, I started with a minimalistic approach. As the project evolved, I iteratively refactored the code, gradually separating responsibilities into multiple files for better organization and readability. This refactoring process also required adjustments to the test suite to ensure everything remained aligned and functional as the project grew. For the database structure, I took an old-school approach and sketched the Entity-Relationship Diagram (ERD) with pencil, which helped me visualize relationships and structure before implementation. This approach allowed the application architecture to evolve naturally while accommodating the requirements of the project.

## AI Tool Usage Disclosure

This project utilized AI-powered development assistance, specifically Claude (Anthropic's AI assistant), throughout the development process. AI assistance was employed for:

- Code debugging and error resolution
- Test case generation and validation
- API endpoint design recommendations
- Laravel best practices guidance
- Database query optimization
- Resource transformation patterns

All AI-generated code suggestions were thoroughly reviewed, tested, and adapted to meet the specific requirements of this FlashcardPro application. The core business logic, architectural decisions, and final implementation choices were made by me, with AI serving as a supplementary development tool.

**Detailed AI interaction logs are available in `prompt-logs.txt` in the project root for complete transparency.**


> Challenge for me was the use of Livewire, as I haven't used it in the past. Symfony has stimulus but I haven't used it either as I'm experienced with VueJS but I liked Livewire, so now I learned that with the help of AI and Laravel docs.
