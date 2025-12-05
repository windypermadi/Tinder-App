# Swagger API Documentation - Panduan

## Tentang Swagger

Swagger (OpenAPI) adalah tools untuk dokumentasi API yang interaktif. Dengan Swagger, Anda bisa:
- Melihat semua endpoint API yang tersedia
- Melihat detail request dan response untuk setiap endpoint
- **Mencoba API langsung dari browser** (tanpa perlu Postman atau cURL)
- Melihat model/schema data

---

## Cara Mengakses Swagger UI

### 1. Pastikan Server Laravel Berjalan
```bash
php artisan serve
```

### 2. Buka Swagger UI di Browser
```
http://localhost:8000/api/documentation
```

Anda akan melihat tampilan interaktif dengan semua endpoint API yang tersedia.

---

## Cara Menggunakan Swagger UI

### 1. Lihat Daftar Endpoint
Setelah membuka Swagger UI, Anda akan melihat endpoint dikelompokkan berdasarkan tag:
- **People** - Endpoint untuk data orang
- **Interactions** - Endpoint untuk like/dislike

### 2. Expand Endpoint
Klik pada endpoint yang ingin Anda lihat untuk melihat detail:
- Parameters (path, query, body)
- Request body schema
- Response codes dan examples

### 3. Try It Out
Untuk mencoba endpoint langsung:

1. Klik tombol **"Try it out"** di pojok kanan endpoint
2. Isi parameter yang diperlukan
3. Klik tombol **"Execute"**
4. Lihat hasilnya di bagian "Responses" di bawah

#### Contoh: Mencoba GET /api/v1/people

1. Expand endpoint `GET /api/v1/people`
2. Klik "Try it out"
3. Isi `per_page` (opsional, misalnya: 5)
4. Klik "Execute"
5. Lihat response body dengan data orang

#### Contoh: Mencoba POST /api/v1/interactions/like

1. Expand endpoint `POST /api/v1/interactions/like`
2. Klik "Try it out"
3. Edit request body:
```json
{
  "from_person_id": 1,
  "to_person_id": 2
}
```
4. Klik "Execute"
5. Lihat response sukses atau error

---

## Generate Ulang Dokumentasi

Setiap kali Anda mengubah annotations di controller, jalankan command ini untuk update dokumentasi:

```bash
php artisan l5-swagger:generate
```

Refresh browser untuk melihat perubahan.

---

## Endpoint yang Tersedia di Swagger

### People Endpoints

1. **GET /api/v1/people**
   - Deskripsi: Get semua orang (dengan pagination)
   - Parameter: `per_page` (optional)

2. **GET /api/v1/people/{id}**
   - Deskripsi: Get detail satu orang
   - Parameter: `id` (required)

3. **GET /api/v1/people/{personId}/recommended**
   - Deskripsi: Get rekomendasi orang
   - Parameter: `personId` (required), `per_page` (optional)

4. **GET /api/v1/people/{personId}/liked-by**
   - Deskripsi: Get list orang yang like user ini
   - Parameter: `personId` (required), `per_page` (optional)

### Interactions Endpoints

1. **POST /api/v1/interactions/like**
   - Deskripsi: Like seseorang
   - Body: `from_person_id`, `to_person_id`

2. **POST /api/v1/interactions/dislike**
   - Deskripsi: Dislike seseorang
   - Body: `from_person_id`, `to_person_id`

---

## Menambahkan Annotations Swagger

Annotations Swagger menggunakan format PHPDoc comments dengan tag `@OA\`.

### Contoh Annotation untuk GET Endpoint

```php
/**
 * @OA\Get(
 *     path="/api/v1/people/{id}",
 *     summary="Get a single person",
 *     description="Get details of a specific person",
 *     tags={"People"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID of the person",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Person not found"
 *     )
 * )
 */
public function show($id)
{
    // method implementation
}
```

### Contoh Annotation untuk POST Endpoint

```php
/**
 * @OA\Post(
 *     path="/api/v1/interactions/like",
 *     summary="Like a person",
 *     tags={"Interactions"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"from_person_id","to_person_id"},
 *             @OA\Property(property="from_person_id", type="integer", example=1),
 *             @OA\Property(property="to_person_id", type="integer", example=2)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Person liked successfully"
 *     )
 * )
 */
public function likePerson(Request $request)
{
    // method implementation
}
```

---

## Konfigurasi Swagger

File konfigurasi: `config/l5-swagger.php`

### Setting Penting:

```php
'routes' => [
    'api' => 'api/documentation',  // URL untuk akses Swagger UI
],

'paths' => [
    'annotations' => [
        base_path('app'),  // Folder untuk scan annotations
    ],
],
```

---

## Info Utama API (di Controller.php)

Informasi umum tentang API didefinisikan di `app/Http/Controllers/Controller.php`:

```php
/**
 * @OA\Info(
 *     title="Tinder App API",
 *     version="1.0.0",
 *     description="API Documentation for Tinder-like Application",
 *     @OA\Contact(
 *         email="admin@example.com"
 *     )
 * )
 * 
 * @OA\Server(
 *     url="http://localhost:8000",
 *     description="Local Development Server"
 * )
 */
```

---

## Troubleshooting

### Swagger UI tidak muncul
1. Pastikan package ter-install: `composer show darkaonline/l5-swagger`
2. Jalankan: `php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"`
3. Generate ulang: `php artisan l5-swagger:generate`

### Perubahan tidak muncul
1. Clear cache: `php artisan cache:clear`
2. Generate ulang: `php artisan l5-swagger:generate`
3. Hard refresh browser (Ctrl + Shift + R)

### Error "Unable to render"
1. Cek syntax annotations di controller
2. Generate ulang: `php artisan l5-swagger:generate`
3. Lihat log error di `storage/logs/laravel.log`

---

## Tips

1. **Gunakan Tags** untuk mengelompokkan endpoint yang sejenis
2. **Tambahkan Examples** di @OA\Property untuk memudahkan testing
3. **Dokumentasikan semua Response Codes** (200, 404, 422, 500, dll)
4. **Update Documentation** setiap kali ada perubahan API

---

## Resources

- L5 Swagger Documentation: https://github.com/DarkaOnLine/L5-Swagger
- OpenAPI Specification: https://swagger.io/specification/
- Swagger Editor (online): https://editor.swagger.io/

---

## Keuntungan Menggunakan Swagger

✅ **Dokumentasi Otomatis** - Tidak perlu menulis dokumentasi terpisah  
✅ **Testing Mudah** - Test API langsung dari browser  
✅ **Always Up-to-date** - Dokumentasi selalu sync dengan code  
✅ **Easy Sharing** - Bagikan URL Swagger ke team/client  
✅ **Standard Format** - OpenAPI adalah format standar industri  

Selamat menggunakan Swagger! 🚀

