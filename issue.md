# Task: Implement Migration & Model for Uoms

## 1. Overview

Buatlah migration dan model Laravel untuk modul `uoms`.

Tujuan utama:

- membuat tabel database untuk satuan ukur / unit of measure
- membuat model Eloquent yang siap dipakai di aplikasi
- menambahkan logging aktivitas perubahan data
- memastikan struktur model mengikuti konvensi Laravel yang umum dipakai

---

## 2. Database Schema

Nama tabel: `uoms`

Kolom yang harus dibuat:

| Column           | Type              | Constraint            | Keterangan                                         |
| ---------------- | ----------------- | --------------------- | -------------------------------------------------- |
| `id`             | `bigint unsigned` | PK, auto increment    | ID utama                                           |
| `code`           | `varchar(30)`     | NOT NULL, UNIQUE      | Kode satuan, contoh: `PCS`, `KG`, `MTR`            |
| `name`           | `varchar(100)`    | NOT NULL              | Nama satuan                                        |
| `decimal_places` | `integer`         | NOT NULL, default `0` | Jumlah digit desimal yang dipakai                  |
| `created_at`     | `timestamp`       | nullable              | Waktu dibuat                                       |
| `updated_at`     | `timestamp`       | nullable              | Waktu diubah                                       |
| `deleted_at`     | `timestamp`       | nullable              | Soft delete, opsional tapi disarankan jika relevan |

Catatan:

- Gunakan `softDeletes()` jika modul ini dianggap data historis / penghapusan bukan permanen.
- Jika tidak relevan, Anda boleh menghilangkan kolom `deleted_at` dan trait `SoftDeletes`.

---

## 3. Migration Requirements

Buat migration dengan nama seperti:

```bash
php artisan make:migration create_uoms_table
```

Pastikan migration berisi:

- `Schema::create('uoms', function (Blueprint $table) { ... })`
- kolom `id`, `code`, `name`, `decimal_places`, timestamps
- `unique()` pada kolom `code`
- `default(0)` pada `decimal_places`
- `softDeletes()` jika dipakai

Contoh struktur umum:

```php
Schema::create('uoms', function (Blueprint $table) {
    $table->id();
    $table->string('code', 30)->unique();
    $table->string('name', 100);
    $table->integer('decimal_places')->default(0);
    $table->timestamps();
    $table->softDeletes();
});
```

---

## 4. Model Laravel

Buat model dengan nama:

```php
App\Models\Uom
```

Nama tabel:

```php
protected $table = 'uoms';
```

### 4.1 Trait yang harus dipakai

Gunakan trait berikut:

- `Illuminate\Database\Eloquent\Factories\HasFactory`
- `Illuminate\Database\Eloquent\SoftDeletes` (jika soft delete dipakai)
- `Spatie\Activitylog\Traits\LogsActivity`

### 4.2 Properti fillable

Deklarasikan `$fillable` seperti berikut:

```php
protected $fillable = [
    'code',
    'name',
    'decimal_places',
];
```

### 4.3 Log Activity

Tambahkan method `getActivitylogOptions()` dengan `LogOptions` dari `spatie/laravel-activitylog`.

Contoh:

```php
use Spatie\Activitylog\LogOptions;

public function getActivitylogOptions(): LogOptions
{
    return LogOptions::defaults()
        ->logFillable()
        ->logOnlyDirty()
        ->dontSubmitEmptyLogs()
        ->useLogName('uom');
}
```

### 4.4 Relasi Model

Deklarasikan relasi secara lengkap di model. Untuk modul `Uom`, kemungkinan relasi yang umum digunakan adalah:

```php
public function products(): HasMany
{
    return $this->hasMany(Product::class, 'uom_id');
}
```

Jika ada modul lain yang berelasi ke `uoms`, buat relasi masing-masing sesuai kebutuhan, misalnya:

```php
public function productDetails(): HasMany
{
    return $this->hasMany(ProductDetail::class, 'uom_id');
}
```

Jika relasi `belongsTo` diperlukan, misalnya `Uom` dimiliki oleh model lain, maka tulis:

```php
public function unitGroup(): BelongsTo
{
    return $this->belongsTo(UnitGroup::class, 'unit_group_id');
}
```

> Jika modul `uoms` tidak punya relasi parent/child, cukup deklarasikan relasi yang benar-benar ada di aplikasi. Jangan menambahkan relasi yang tidak digunakan.

---

## 5. Implementasi Langkah Demi Langkah

1. Buat migration `create_uoms_table`.
2. Tambahkan kolom `code`, `name`, `decimal_places`, timestamps, dan `softDeletes()` jika relevan.
3. Buat file model `app/Models/Uom.php`.
4. Tambahkan trait `HasFactory`, `SoftDeletes`, dan `LogsActivity`.
5. Tambahkan property `$fillable`.
6. Tambahkan method `getActivitylogOptions()`.
7. Tambahkan relasi yang benar-benar diperlukan, seperti `hasMany()` atau `belongsTo()`.
8. Jalankan migration:

```bash
php artisan migrate
```

9. Validasi model bisa dibuat dan data tersimpan tanpa error.

---

## 6. Definition of Done

Checklist berikut harus terpenuhi:

- [ ] Migration `uoms` berhasil dibuat dan dijalankan
- [ ] Tabel memiliki kolom `id`, `code`, `name`, `decimal_places`, `created_at`, `updated_at`
- [ ] `code` bersifat unique
- [ ] `decimal_places` default `0`
- [ ] `SoftDeletes` dipakai jika relevan
- [ ] Model `Uom` memiliki `$fillable`
- [ ] Model `Uom` memiliki `getActivitylogOptions()` dari `LogOptions`
- [ ] Relasi model sudah dideklarasikan dengan benar
- [ ] Data bisa disimpan dan diubah tanpa error

---

## 7. Contoh Model Sederhana

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Uom extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $table = 'uoms';

    protected $fillable = [
        'code',
        'name',
        'decimal_places',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('uom');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'uom_id');
    }
}
```

---

## 8. Catatan Penting untuk Junior Programmer / AI Murah

- Fokus pada kebutuhan minimal yang diminta.
- Jangan menambahkan relasi yang tidak jelas penggunaannya.
- Jika tidak ada kebutuhan khusus, cukup gunakan `softDeletes()` sesuai prinsip aplikasi.
- Pastikan nama file model, migration, dan tabel konsisten dengan Laravel naming convention.
- Gunakan `unique()` untuk `code` agar data tidak duplikat.
- Gunakan `LogOptions` agar perubahan data tercatat.
