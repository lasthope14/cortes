# Contexto Completo del Proyecto

## 1. Objetivo de negocio
Construir una app para gestionar cortes de obra en Laravel con MySQL.

La app debe permitir:
- importar el contrato desde el Excel del cliente
- registrar avances por item de obra desde campo
- adjuntar una foto de lo ejecutado y otra del plano record
- consolidar el corte por rango de fechas
- generar la caratula del Excel con el formato exacto solicitado
- generar memorias de cantidades por partida con el desglose capturado

## 2. Insumos originales revisados
Fuera de este repo, en la carpeta de trabajo original, se revisaron estos archivos:
- `corte medellin hacer.xlsx`
- `Diagrama en blanco.json`
- `caratula.csv`
- `excel_info.txt`

Esos archivos no se subieron al repo para no mezclar artefactos del cliente con el codigo. El contexto funcional ya quedo documentado aqui.

## 3. Hallazgos clave del Excel

### Hojas encontradas
- `Caratula de Estimaciones`
- `24.2.3.3.12`
- `24.2.3.4.1`

### Estructura observada
- La caratula tiene `807` filas y `26` columnas utiles.
- Las hojas secundarias tienen `102` filas y `25` columnas utiles.
- La caratula mezcla:
  - cabecera contractual
  - resumen de estimacion
  - jerarquia de capitulos / subcapitulos
  - partidas medibles

### Lectura funcional correcta
- Las dos hojas secundarias son ejemplos del formato en que debe presentarse la memoria de cantidades por item.
- No deben interpretarse como un catalogo completo de hojas existentes.
- La regla correcta es: por cada partida con ejecucion se debe poder generar una hoja de memoria con todas las filas que expliquen esa cantidad.

### Implicacion de modelado
- No basta guardar una cantidad total por concepto.
- Se necesita guardar filas de desglose por item para luego reconstruir la memoria.
- La identidad del contrato debe conservar la fila importada del Excel, no depender solo de `concept_code`.

## 4. Decisiones de dominio tomadas

### Contrato importado
Se modela con:
- `projects`
- `excel_templates`
- `contract_lines`
- `contract_line_addendums`

### Captura en campo
Se modela con:
- `field_reports`
- `field_report_rows`
- `evidences`

### Corte emitido
Se modela con:
- `estimates`
- `estimate_line_snapshots`

## 5. Regla operacional importante
La asignacion al corte se hace al nivel de `field_report_rows`, no al nivel del reporte completo.

Esto permite:
- evitar que una ejecucion termine en dos cortes
- consolidar por rango de fechas
- mantener trazabilidad exacta de cada fila ejecutada
- regenerar memorias por partida con sus evidencias

## 6. Estructura de datos relevante

### `contract_lines`
Representa cada linea importada de la caratula.

Campos relevantes:
- `display_order`
- `excel_row`
- `row_type`
- `parent_id`
- `cost_center`
- `item_number`
- `concept_code`
- `description`
- `unit`
- `budget_quantity`
- `unit_price`
- `budget_amount`

### `field_report_rows`
Representa una fila de memoria de cantidades capturada por el ingeniero.

Campos relevantes:
- `contract_line_id`
- `work_date`
- `sequence`
- `element`
- `axis`
- `level`
- `plan_reference`
- `length`
- `width`
- `height`
- `area`
- `volume`
- `weight`
- `quantity`
- `subtotal`
- `element_count`
- `observations`

### `evidences`
Representa evidencia de una fila ejecutada.

Tipos:
- `work`
- `plan`

## 7. Estado tecnico actual

### Ya implementado
- Laravel 12
- Migraciones del dominio
- Modelos Eloquent
- Enums para estados y tipos
- Dashboard inicial
- Servicio base de importacion
- Servicio base de exportacion
- Analizador real del workbook via Laravel
- Configuracion `.env.example` orientada a MySQL
- Pruebas automatizadas pasando

### Comando disponible
```bash
php artisan workbook:analyze "ruta\\al\\archivo.xlsx"
```

## 8. Resultado de validacion hecho antes del push
- `php artisan migrate:fresh --force`
- `php artisan test`

Resultado:
- migraciones correctas
- 3 pruebas pasando

## 9. Archivos clave del proyecto
- `app/Models`
- `database/migrations`
- `app/Services/Imports/ContractWorkbookImporter.php`
- `app/Services/Imports/WorkbookStructureAnalyzer.php`
- `app/Services/Exports/EstimateWorkbookExporter.php`
- `routes/console.php`
- `resources/views/dashboard.blade.php`
- `docs/architecture.md`

## 10. Siguiente tramo de trabajo recomendado

### Prioridad 1
Implementar el importador real del Excel:
- leer cabecera
- crear `project`
- crear `contract_lines`
- detectar filas de grupo e item
- preservar `excel_row` y `display_order`

### Prioridad 2
Crear CRUD base para:
- proyectos
- lineas contractuales
- reportes de campo
- filas de memoria
- evidencias

### Prioridad 3
Implementar generacion del corte:
- seleccionar rango
- tomar filas no asignadas
- consolidar por partida
- poblar `estimate_line_snapshots`

### Prioridad 4
Implementar exportacion sobre plantilla:
- llenar caratula
- crear hojas de memoria por item ejecutado
- adjuntar referencias de evidencia si el cliente lo requiere

## 11. Nota para continuar desde otra maquina
Si vas a seguir desde casa:
1. clona el repo
2. crea el `.env`
3. configura MySQL
4. corre migraciones
5. usa el comando `workbook:analyze` con el Excel local del cliente

El repo ya queda listo como base de continuidad del desarrollo.
