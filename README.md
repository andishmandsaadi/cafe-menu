# Cafe Menu Management System

A **Cafe Menu Management System** built with **Laravel 11**, **Vue 3**, **Bootstrap**, **Axios**, **SweetAlert2**, and **Pinia**. This project provides a platform for cafe owners to manage their menu categories and products.

## 🚀 Features
- **Admin Panel** (Vue.js) - `/admin`
- **User-facing Interface** (Blade) - `/`
- **Category & Product Management**
- **SQLite Database**
- **Bootstrap for UI Design**
- **Pinia for State Management** (Admin Panel)
- **Axios for API Requests**
- **SweetAlert2 for Notifications**
- **No Authentication Implemented**

## 📌 Technology Stack
- **Backend**: Laravel 11
- **Frontend (Admin Panel)**: Vue 3, Pinia, Axios, SweetAlert2, Bootstrap
- **Frontend (User Part)**: Blade, Bootstrap
- **Database**: SQLite

## 🛠️ Installation & Setup
### 1️⃣ Clone the repository
```sh
 git clone https://github.com/andishmandsaadi/cafe-menu.git
 cd cafe-menu
```

### 2️⃣ Install Dependencies
```sh
 composer install
 npm install
```

### 3️⃣ Set Up Environment
```sh
 cp .env.example .env
```
Update `.env` file:
- Set database connection to `sqlite`
```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite
```

### 4️⃣ Generate Application Key
```sh
 php artisan key:generate
```

### 5️⃣ Run Database Migrations
```sh
 php artisan migrate --seed
```

### 6️⃣ Compile Vue Assets
```sh
 npm run build
```

### 7️⃣ Serve the Application
```sh
 php artisan serve
```

## 🖥️ Access the Application
- **User Interface:** [`http://127.0.0.1:8000/`](http://127.0.0.1:8000/)
- **Admin Panel:** [`http://127.0.0.1:8000/admin`](http://127.0.0.1:8000/admin)

---

Happy coding! 🎉

