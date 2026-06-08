# MonBlog - Application Laravel

Un blog moderne et complet construit avec Laravel 11, Bootstrap 5 et PHP 8.2+.

## Fonctionnalites

- **Blog public** : Articles, categories, recherche, commentaires
- **Panel admin** : Dashboard, gestion des articles/categories/commentaires
- **Authentification** : Login admin securise
- **Design moderne** : Bootstrap 5 avec design editorial

## Pre-requis

- PHP 8.2+
- Composer
- MySQL ou MariaDB
- Node.js (optionnel, pour assets)

## Installation

### 1. Cloner ou telecharger le projet

```bash
cd laravel-blog
```

### 2. Installer les dependances PHP

```bash
composer install
```

### 3. Configurer l'environnement

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configurer la base de donnees

Editez le fichier `.env` :

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mon_blog
DB_USERNAME=root
DB_PASSWORD=
```

Creez la base de donnees :

```sql
CREATE DATABASE mon_blog CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 5. Executer les migrations et le seeder

```bash
php artisan migrate
php artisan db:seed
php artisan storage:link
```

### 6. Lancer le serveur

```bash
php artisan serve
```

## Acces

- **Blog public** : http://localhost:8000
- **Admin** : http://localhost:8000/login
  - Email : `admin@blog.com`
  - Mot de passe : `password`

## Structure du projet

```
laravel-blog/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php
│   │   │   ├── BlogController.php
│   │   │   └── Admin/
│   │   │       ├── DashboardController.php
│   │   │       ├── PostController.php
│   │   │       ├── CategoryController.php
│   │   │       └── CommentController.php
│   │   └── Middleware/
│   │       └── AdminMiddleware.php
│   └── Models/
│       ├── User.php
│       ├── Post.php
│       ├── Category.php
│       └── Comment.php
├── database/
│   ├── migrations/
│   └── seeders/
│       └── DatabaseSeeder.php
├── resources/views/
│   ├── layouts/
│   │   ├── app.blade.php
│   │   └── admin.blade.php
│   ├── blog/
│   │   ├── index.blade.php
│   │   ├── show.blade.php
│   │   ├── category.blade.php
│   │   ├── search.blade.php
│   │   └── partials/
│   │       └── sidebar.blade.php
│   ├── admin/
│   │   ├── dashboard.blade.php
│   │   ├── posts/
│   │   ├── categories/
│   │   └── comments/
│   └── auth/
│       └── login.blade.php
├── routes/
│   └── web.php
└── bootstrap/
    └── app.php
```

## Licence

MIT
