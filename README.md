# Laravel Cocktail App

Una aplicación web construida con Laravel + Alpine.js + TailwindCSS que te permite explorar cócteles desde una API externa, guardarlos como favoritos y administrarlos fácilmente.

## 🚀 Características

- Búsqueda por letra desde [TheCocktailDB API](https://www.thecocktaildb.com/)
- Guardar cócteles favoritos
- Editar y eliminar favoritos
- SweetAlert para retroalimentación de acciones
- UI responsiva con TailwindCSS
- Autenticación con Laravel Breeze
- Base de datos MySQL en AWS, 

## 📦 Instalación

```bash
git clone https://github.com/tu-usuario/laravel-cocktail-app.git
cd laravel-cocktail-app
composer install
cp .env.example .env
php artisan key:generate
composer install
cp .env.example .env
php artisan key:generate

# Configura tus credenciales de base de datos AWS en .env y en .env.example se deja tambien una  coneccion con una base de datos de pruebas 

npm install
npm run dev

php artisan serve