# 📚 Laravel + Inertia + Vue.js CRUD Nilai & Siswa

Aplikasi sederhana untuk manajemen **data siswa** dan **nilai** menggunakan:

- **Backend**: Laravel 12
- **Frontend**: Vue.js 3 + Inertia.js
- **UI**: shadcn/ui + TailwindCSS

## ✅ Requirement

Pastikan environment sudah terpasang:

- PHP >= 8.2
- Composer >= 2.x
- Node.js >= 22.x & npm >= 9.x
- PostgreSQL
- Git

## 🚀 Features

- CRUD **Siswa** (create, read, update, delete)
- CRUD **Nilai**
    - Auto-fill kelas berdasarkan siswa
    - Validasi nilai (0–100)
- Import & Export Excel (Nilai)
    - Format: `No, Nama, Kelas, Mapel, Nilai`
- Dashboard dengan filter:
    - Pencarian global
    - Filter kelas
    - Filter mapel
- Flash message untuk **success & error**
- Responsive UI dengan Tailwind + shadcn/ui

## 🏛️ Arsitektur

Aplikasi ini menggunakan pola **Laravel + Inertia + Vue**.

**Alasan memilih stack ini**:

- **Project/tugas ini cenderung aplikasi sederhana**
- **Development cepat** → tidak perlu membuat API terpisah.
- **Tim developer nya sedikit** (saya sendiri).
- **Konfigurasi lebih mudah**.

## ⚙️ Instalasi & Menjalankan Project

1. **Clone repo**
    ```bash
    git clone https://github.com/anrisys/management_nilai.git
    cd sistem-management-nilai
    ```
2. **Install dependency PHP**
    ```bash
    composer install
    ```
3. **Install dependency javascript**
    ```bash
    npm install
    ```
4. **Buat file .env**
    ```bash
    cp .env.example .env
    ```
    Atur konfigurasi database sebagai contoh:
    ```bash
    DB_CONNECTION=pgsql
    DB_HOST=127.0.0.1
    DB_PORT=5432
    DB_DATABASE=siswa_app
    DB_USERNAME=postgres
    DB_PASSWORD=mysecretpassword
    ```
    Pastikan database nya sudah ada di postgre.
5. **Generate key**
    ```bash
    php artisan key:generate
    ```
6. **Jalankan migrasi + seeder**

    ```bash
    # Run migrations
    php artisan migrate

    # Seed database dengan sample data
    php artisan db:seed
    ```

7. **Jalankan migrasi + seeder**

    ```bash
    # Run migrations
    php artisan migrate

    # Seed database dengan sample data
    php artisan db:seed
    ```

8. **Jalankan server laravel**
    ```bash
    Jalankan server Laravel
    ```
9. **Jalankan dev server frontend**
    ```bash
    npm run dev
    ```
10. **Buka aplikasi di browser: http:/localhost:8000**

# Akun demo

Seeder akan otomatis menambahkan akun berikut:

- Email : demo@example.com
- Password : demo12345

# 🐛 Troubleshooting

Isu yang mungkin ditemui: **lightningcss module untuk windows tidak ditemukan**

    ```bash
    Failed to load config from: ..../sistem-management-nilai/vite.config.ts
    error when starting dev server:
    Error: Cannot find module '../lightningcss.win32-x64-msvc.node'
    ```

Cara solve:

- Pastikan menggunakan node.js versi LTS
- Install: Install the Microsoft Visual C++ Redistributable. Di link: [https://learn.microsoft.com/en-us/cpp/windows/latest-supported-vc-redist?view=msvc-170](https://learn.microsoft.com/en-us/cpp/windows/latest-supported-vc-redist?view=msvc-170)
- Selanjutnya, jalankan/eksekusi command:
    ```bash
    rm -rf node_modules package-lock.json
    npm cache clean --force
    npm install
    ```
