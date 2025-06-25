
# FlashcardPro

FlashcardPro is a Laravel-based web application that allows users to create, manage, and study flashcards.  
The main interface is built using **Laravel**, **Livewire**, and **TailwindCSS**.

---

## 👨‍💻 Developer Information

**Name:** Luis Mendoza  
**Email:** luis@atomoweb.com

---

## ⚙️ Stack & Tools

- **PHP:** 8.4
- **Laravel:** v12 (latest)
- **Laravel Breeze** for authentication
- **Laravel Sanctum** for API token authentication
- **Breeze:Livewire** for UI scaffolding
- **Tailwind CSS** for frontend styling
- **Docker + Laravel Sail** for local development
- **Swagger** for API documentation

---

## ✅ Requirements

- Docker installed
- Node.js and NPM (if not using Sail for frontend)
- Git

---

## 🚀 Getting Started

1. **Clone the repository**

   ```bash
   git clone git@github.com:luisatomo/FlashcardPro.git
   cd FlashcardPro
   ```

2. **Start Laravel Sail**

   ```bash
   ./vendor/bin/sail up -d
   ```

3. **Install PHP dependencies**

   ```bash
   ./vendor/bin/sail composer install
   ```

4. **Copy and configure `.env` file**

   ```bash
   cp .env.example .env
   ./vendor/bin/sail artisan key:generate
   ```

   Make sure your database credentials in `.env` match your Docker configuration.

5. **Run migrations and seed data**

   ```bash
   ./vendor/bin/sail artisan migrate:fresh --seed
   ```

6. **Install frontend dependencies**

   ```bash
   ./vendor/bin/sail npm install
   ./vendor/bin/sail npm run dev
   ```

7. **Run tests**

   ```bash
   ./vendor/bin/sail artisan test
   ```

8. **Linting (optional)**

    - Check code style (PSR-12):

      ```bash
      ./vendor/bin/sail composer lint
      ```

    - Fix auto-correctable issues:

      ```bash
      ./vendor/bin/sail composer lint-fix
      ```

---

## 🧠 Architecture Notes

> This project was designed iteratively. I began with a minimal approach due to the scope, gradually introducing structure and separating responsibilities as complexity grew.

- The architecture was inspired by **Domain-Driven Design (DDD)** practices.
- Early-stage planning included a hand-drawn **Entity Relationship Diagram (ERD)**.
- Test suites were maintained and updated alongside feature development to ensure code reliability.

---

## 🤖 AI Tool Usage Disclosure

AI (Claude from Anthropic) was used as a coding assistant for:

- Code debugging
- Test case generation
- API design ideas
- Best practices in Laravel
- Query optimizations
- Pattern suggestions

> All AI-generated suggestions were reviewed, adapted, and tested.  
> Core logic and decisions were authored by me.

📁 `prompt-logs.txt` (in the root) contains a full log of AI-assisted interactions.

---

## 📘 API Documentation (Swagger)

A Swagger UI for API endpoints is available at:

```
[domain]/api-doc.html
```

Endpoints include:

- `/api/login`
- `/api/decks`
- `/api/flashcards`

Use the **login endpoint** to generate a Bearer token for authenticating subsequent requests.

---

## 🌐 Live Demo

You can access the live application at:

👉 [https://flashcardpro.atomoweb.com](https://flashcardpro.atomoweb.com)

---

## 📌 Notes

> This was my first real experience using **Livewire**, having previously worked mostly with VueJS.  
> I found Livewire intuitive and enjoyable, and thanks to Laravel docs and AI assistance, I was able to quickly pick it up and implement dynamic features effectively.

---


# FlashcardPro

FlashcardPro is a Laravel-based web application that allows users to create, manage, and study flashcards.  
The main interface is built using **Laravel**, **Livewire**, and **TailwindCSS**.

---

## 👨‍💻 Developer Information

**Name:** Luis Mendoza  
**Email:** luis@atomoweb.com

---

## ⚙️ Stack & Tools

- **PHP:** 8.4
- **Laravel:** v12 (latest)
- **Laravel Breeze** for authentication
- **Laravel Sanctum** for API token authentication
- **Breeze:Livewire** for UI scaffolding
- **Tailwind CSS** for frontend styling
- **Docker + Laravel Sail** for local development
- **Swagger** for API documentation

---

## ✅ Requirements

- Docker installed
- Node.js and NPM (if not using Sail for frontend)
- Git

---

## 🚀 Getting Started

1. **Clone the repository**

   ```bash
   git clone git@github.com:luisatomo/FlashcardPro.git
   cd FlashcardPro
   ```

2. **Start Laravel Sail**

   ```bash
   ./vendor/bin/sail up -d
   ```

3. **Install PHP dependencies**

   ```bash
   ./vendor/bin/sail composer install
   ```

4. **Copy and configure `.env` file**

   ```bash
   cp .env.example .env
   ./vendor/bin/sail artisan key:generate
   ```

   Make sure your database credentials in `.env` match your Docker configuration.

5. **Run migrations and seed data**

   ```bash
   ./vendor/bin/sail artisan migrate:fresh --seed
   ```

6. **Install frontend dependencies**

   ```bash
   ./vendor/bin/sail npm install
   ./vendor/bin/sail npm run dev
   ```

7. **Run tests**

   ```bash
   ./vendor/bin/sail artisan test
   ```

8. **Linting (optional)**

    - Check code style (PSR-12):

      ```bash
      ./vendor/bin/sail composer lint
      ```

    - Fix auto-correctable issues:

      ```bash
      ./vendor/bin/sail composer lint-fix
      ```

---

## 🧠 Architecture Notes

> This project was designed iteratively. I began with a minimal approach due to the scope, gradually introducing structure and separating responsibilities as complexity grew.

- The architecture was inspired by **Domain-Driven Design (DDD)** practices.
- Early-stage planning included a hand-drawn **Entity Relationship Diagram (ERD)**.
- Test suites were maintained and updated alongside feature development to ensure code reliability.

---

## 🤖 AI Tool Usage Disclosure

AI (Claude from Anthropic) was used as a coding assistant for:

- Code debugging
- Test case generation
- API design ideas
- Best practices in Laravel
- Query optimizations
- Pattern suggestions

> All AI-generated suggestions were reviewed, adapted, and tested.  
> Core logic and decisions were authored by me.

📁 `prompt-logs.txt` (in the root) contains a full log of AI-assisted interactions.

---

## 📘 API Documentation (Swagger)

A Swagger UI for API endpoints is available at:

```
[domain]/api-doc.html
```

Endpoints include:

- `/api/login`
- `/api/decks`
- `/api/flashcards`

Use the **login endpoint** to generate a Bearer token for authenticating subsequent requests.

---

## 🌐 Live Demo

You can access the live application at:

👉 [https://flashcardpro.atomoweb.com](https://flashcardpro.atomoweb.com)
👉 [https://flashcardpro.atomoweb.com/api-doc.html](https://flashcardpro.atomoweb.com/api-doc.html)
---

## 📌 Notes

> This was my first real experience using **Livewire**, having previously worked mostly with VueJS.  
> I found Livewire intuitive and enjoyable, and thanks to Laravel docs and AI assistance, I was able to quickly pick it up and implement dynamic features effectively.

---

## ✅ Credentials

user: jane@example.com
password: password

You can find more users in the seed file: database/seeders/UserSeeder.php
