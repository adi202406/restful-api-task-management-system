# Status API

Dokumentasi endpoint status yang berada dalam lingkup `board`.

## Base URL

`/api/workspaces/{workspace}/boards/{board}/statuses`

> `{workspace}` adalah slug workspace yang valid.
> `{board}` adalah ID board yang valid.

## Autentikasi

Semua endpoint status menggunakan middleware `auth:sanctum`.

## Sumber Daya Status

Response `StatusResource` mengembalikan:

- `id`
- `board_id`
- `name`
- `color`
- `position`
- `created_at`
- `updated_at`

## Endpoints

### List Status

- Method: `GET`
- URL: `/api/workspaces/{workspace}/boards/{board}/statuses`

Response:

```json
[
  {
    "id": 1,
    "board_id": 12,
    "name": "To Do",
    "color": "#2563eb",
    "position": 1,
    "is_default": true,
    "created_at": "2026-05-27 11:00:00",
    "updated_at": "2026-05-27 11:00:00"
  },
  {
    "id": 2,
    "board_id": 12,
    "name": "In Progress",
    "color": "#f59e0b",
    "position": 2,
    "is_default": false,
    "created_at": "2026-05-27 11:05:00",
    "updated_at": "2026-05-27 11:05:00"
  }
]
```

### Create Status

- Method: `POST`
- URL: `/api/workspaces/{workspace}/boards/{board}/statuses`

Request body:

```json
{
  "board_id": 12,
  "name": "Done",
  "color": "#16a34a",
  "position": 3,
  "is_default": false
}
```

Validasi:

- `board_id` wajib
- `board_id` harus ada pada tabel `boards`
- `board_id` harus cocok dengan `{board}` pada route
- `name` wajib, maksimum 255 karakter
- `color` opsional, format `#rrggbb`
- `position` opsional, integer
- `is_default` opsional, boolean

Response:

```json
{
  "id": 3,
  "board_id": 12,
  "name": "Done",
  "color": "#16a34a",
  "position": 3,
  "is_default": false,
  "created_at": "2026-05-27 11:10:00",
  "updated_at": "2026-05-27 11:10:00"
}
```

### Get Status

- Method: `GET`
- URL: `/api/workspaces/{workspace}/boards/{board}/statuses/{status}`

Response:

```json
{
  "id": 2,
  "board_id": 12,
  "name": "In Progress",
  "color": "#f59e0b",
  "position": 2,
  "is_default": false,
  "created_at": "2026-05-27 11:05:00",
  "updated_at": "2026-05-27 11:05:00"
}
```

### Update Status

- Method: `PUT` atau `PATCH`
- URL: `/api/workspaces/{workspace}/boards/{board}/statuses/{status}`

Request body:

```json
{
  "board_id": 12,
  "name": "In Review",
  "color": "#ea580c",
  "position": 2,
  "is_default": false
}
```

Response:

```json
{
  "id": 2,
  "board_id": 12,
  "name": "In Review",
  "color": "#ea580c",
  "position": 2,
  "is_default": false,
  "created_at": "2026-05-27 11:05:00",
  "updated_at": "2026-05-27 11:15:00"
}
```

### Delete Status

- Method: `DELETE`
- URL: `/api/workspaces/{workspace}/boards/{board}/statuses/{status}`

Response:

- Status: `204 No Content`

## Catatan

- `board_id` harus sama dengan ID board yang terkait dengan route.
- Jika `board_id` tidak cocok dengan route `{board}`, API akan merespon `422`.
- Setiap `status` terkait dengan 1 `board`.
- Kartu (`card`) dapat memakai `status_id` untuk menghubungkan satu status.
