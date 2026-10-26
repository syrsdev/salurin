# Salurin

## Instalasi

Clone Repo ini

```bash
  git clone https://github.com/syrsdev/salurin
```

Pindah direktori

```bash
  cd salurin
```

Install depedensi

```bash
  composer install
  npm install
```

Salin file .env.example

```bash
  cp .env.example .env

  php artisan key:generate
```

Buat database dan hubungkan di file .env

```bash
  DB_DATABASE=salurin
```

Jalankan migrasi

```bash
  php artisan migrate --seed
  # atau
  php artisan migrate:fresh --seed
```

Jalankan server

```bash
  composer run dev
```
