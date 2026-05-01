# Test Data Versions

Laravel API with polymorphic data versioning.

---

## English

### Run

```bash
docker compose up -d
docker exec app php artisan migrate
docker exec app php artisan db:seed
```

Server starts at `http://localhost:8000`.

### API

| Method | URL | Description |
|--------|-----|-------------|
| `POST` | `/api/companies` | Create or update company |
| `GET` | `/api/companies/{edrpou}/versions` | Get company version history |

**POST `/api/companies` body:**
```json
{
    "name": "Company Name",
    "edrpou": "12345678",
    "address": "123 Main St"
}
```

### Migrations

Located in `database/migrations/`:

| File | Description |
|------|-------------|
| `2026_05_01_121602_create_companies_table.php` | Companies table |
| `2026_05_01_130000_create_versions_table.php` | Versions table (polymorphic) |
| `2026_05_01_122535_create_personal_access_tokens_table.php` | Sanctum tokens |

### Versioning

Versioning is implemented via the `HasVersions` trait and a shared polymorphic `versions` table (not `company_versions`). This was done so versioning would work with **any model** — not only companies. Any future route or model can use the same mechanism by adding `use HasVersions` to the model.

---

## Українська

### Запуск

```bash
docker compose up -d
docker exec app php artisan migrate
docker exec app php artisan db:seed
```

Сервер доступний за адресою `http://localhost:8000`.

### API

| Метод | URL | Опис |
|-------|-----|------|
| `POST` | `/api/companies` | Створити або оновити компанію |
| `GET` | `/api/companies/{edrpou}/versions` | Отримати історію версій компанії |

**POST `/api/companies` тіло запиту:**
```json
{
    "name": "Назва компанії",
    "edrpou": "12345678",
    "address": "вул. Хрещатик, 1"
}
```

### Міграції

Знаходяться в `database/migrations/`:

| Файл | Опис |
|------|------|
| `2026_05_01_121602_create_companies_table.php` | Таблиця компаній |
| `2026_05_01_130000_create_versions_table.php` | Таблиця версій (поліморфна) |
| `2026_05_01_122535_create_personal_access_tokens_table.php` | Токени Sanctum |

### Версійність

Версійність реалізована через трейт `HasVersions` та спільну поліморфну таблицю `versions` (не `company_versions`). Це було зроблено з метою забезпечити версійність для роботи з будь-якою моделлю — не лише з компаніями. Будь-який майбутній маршрут або модель може використовувати той самий механізм, додавши `use HasVersions` до моделі.
