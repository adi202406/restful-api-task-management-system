# Workspace Label API

Dokumentasi endpoint label yang sekarang berada dalam lingkup `workspace`.

## Base URL

`/api/workspaces/{workspace}/labels`

> `{workspace}` adalah slug workspace yang valid.

## Autentikasi

Semua endpoint label menggunakan middleware `auth:sanctum`.

## Sumber Daya Label

Response `LabelResource` sekarang mengembalikan:

- `id`
- `workspace_id`
- `name`
- `color`
- `created_at`
- `updated_at`

## Endpoints

### List Labels

- Method: `GET`
- URL: `/api/workspaces/{workspace}/labels`

Response:

```json
[
  {
    "id": 1,
    "workspace_id": 10,
    "name": "Urgent",
    "color": "#ff0000",
    "created_at": "2026-05-27 10:00:00",
    "updated_at": "2026-05-27 10:00:00"
  }
]
```

### Create Label

- Method: `POST`
- URL: `/api/workspaces/{workspace}/labels`

Request body:

```json
{
  "workspace_id": 10,
  "name": "Bug",
  "color": "#f59e0b"
}
```

Validasi:

- `workspace_id` wajib
- `workspace_id` harus ada pada tabel `workspaces`
- `workspace_id` harus cocok dengan `{workspace}` pada route
- `name` wajib, maksimum 255 karakter
- `color` opsional, format `#rrggbb`

Response:

```json
{
  "id": 2,
  "workspace_id": 10,
  "name": "Bug",
  "color": "#f59e0b",
  "created_at": "2026-05-27 10:05:00",
  "updated_at": "2026-05-27 10:05:00"
}
```

### Get Label

- Method: `GET`
- URL: `/api/workspaces/{workspace}/labels/{label}`

Response:

```json
{
  "id": 2,
  "workspace_id": 10,
  "name": "Bug",
  "color": "#f59e0b",
  "created_at": "2026-05-27 10:05:00",
  "updated_at": "2026-05-27 10:05:00"
}
```

### Update Label

- Method: `PUT` atau `PATCH`
- URL: `/api/workspaces/{workspace}/labels/{label}`

Request body:

```json
{
  "workspace_id": 10,
  "name": "Bug Fix",
  "color": "#1d4ed8"
}
```

Response:

```json
{
  "id": 2,
  "workspace_id": 10,
  "name": "Bug Fix",
  "color": "#1d4ed8",
  "created_at": "2026-05-27 10:05:00",
  "updated_at": "2026-05-27 10:10:00"
}
```

### Delete Label

- Method: `DELETE`
- URL: `/api/workspaces/{workspace}/labels/{label}`

Response:

- Status: `204 No Content`

## Catatan

- `workspace_id` harus sama dengan ID workspace yang terkait dengan semua request label.
- Jika `workspace_id` tidak cocok dengan route `{workspace}`, API akan merespon `422`.
