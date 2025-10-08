
## Tech Stack

- Laravel 12
- PHP 8.2
- MySQL
- Alpine.js
- Tailwind CSS
- Flux UI

## Installation

1. Clone the repository:
```bash
git clone https://github.com/youcefskr/MiniCRM.git
```

2. Install dependencies:
```bash
composer install
npm install
```

3. Create environment file:
```bash
cp .env.example .env
```

4. Generate application key:
```bash
php artisan key:generate
```

5. Run migrations and seeders:
```bash
php artisan migrate:fresh --seed
```

6. Start the development server:
```bash
php artisan serve
```

## Default Admin Credentials

- Email: admin@gmail.com
- Password: admin



