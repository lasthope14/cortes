# Cortes Medellin

Aplicacion Laravel + MySQL para gestionar cortes de obra, memorias de cantidades por partida, evidencias fotograficas y exportacion del Excel del cliente.

## Estado actual
- Proyecto Laravel 12 creado y validado.
- Dominio principal modelado en migraciones y modelos Eloquent.
- Dependencia de Excel instalada: `maatwebsite/excel`.
- Comando base para analizar workbooks: `php artisan workbook:analyze`.
- Dashboard inicial disponible en `/`.
- Suite de pruebas pasando.

## Estructura funcional ya creada
- `projects`
- `excel_templates`
- `contract_lines`
- `contract_line_addendums`
- `field_reports`
- `field_report_rows`
- `evidences`
- `estimates`
- `estimate_line_snapshots`

## Documentacion
- Contexto funcional y tecnico: [docs/project-context.md](docs/project-context.md)
- Arquitectura inicial: [docs/architecture.md](docs/architecture.md)

## Requisitos
- PHP 8.2+
- Composer
- MySQL 8+

## Arranque local
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

## Configuracion base
El `.env.example` ya viene orientado a MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cortes_medellin
DB_USERNAME=root
DB_PASSWORD=
```

## Comandos utiles
```bash
php artisan test
php artisan workbook:analyze "ruta\\al\\archivo.xlsx"
php artisan contract:import "ruta\\al\\archivo.xlsx"
```

## Siguiente paso recomendado
Implementar el importador real de la hoja `Caratula de Estimaciones` hacia `projects` y `contract_lines`, preservando orden, fila original y jerarquia.
