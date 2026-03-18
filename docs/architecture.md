# Arquitectura Inicial

## Objetivo
Construir una aplicacion Laravel + MySQL que:

1. Importe la caratula contractual del Excel del cliente.
2. Permita capturar memorias de cantidades por partida desde campo.
3. Almacene evidencias de obra y plano record por fila ejecutada.
4. Genere cortes congelados y exporte el Excel final con el formato del cliente.

## Stack base ya instalado
- Laravel 12
- PHP 8.2
- maatwebsite/excel
- phpoffice/phpspreadsheet

## Hallazgos que condicionan el modelo
- La hoja `Caratula de Estimaciones` mezcla cabecera, jerarquia presupuestal y partidas medibles.
- La clave del concepto no siempre puede tomarse como identificador unico de negocio.
- Las hojas secundarias son ejemplos del formato de memoria de cantidades por item.
- La memoria se debe llenar con varias filas de detalle por partida, no solo con una cantidad total.

## Modelo de datos

### `projects`
Cabecera contractual principal del proyecto.

### `excel_templates`
Versiones de la plantilla original y hojas auxiliares que se usaran como base de exportacion.

### `contract_lines`
Una fila por linea importada de la caratula:
- conserva `excel_row` y `display_order`
- distingue `group` vs `item`
- mantiene la jerarquia mediante `parent_id`

### `contract_line_addendums`
Adiciones por linea contractual, no por codigo general.

### `field_reports`
Cabecera de reporte de campo del ingeniero.

### `field_report_rows`
Desglose real de la memoria por partida:
- eje
- nivel
- largo/ancho/alto
- area/volumen/peso
- cantidad
- subtotal
- numero de elementos
- observaciones
- referencia al plano

### `evidences`
Evidencia asociada a cada fila ejecutada:
- `work`
- `plan`

### `estimates`
Cabecera del corte emitido.

### `estimate_line_snapshots`
Snapshot congelado de la caratula del corte:
- contractual
- addendums
- acumulado anterior
- ejecutado en el corte
- acumulado
- faltante

## Flujo objetivo

### 1. Importacion
- cargar plantilla original
- leer caratula
- crear `projects`
- crear `contract_lines`
- identificar items que requeriran memoria por partida

### 2. Captura en campo
- seleccionar proyecto
- registrar reporte de campo
- agregar filas por partida ejecutada
- adjuntar foto de obra y foto de plano record

### 3. Generacion del corte
- seleccionar rango de fechas
- tomar filas no asignadas
- consolidar cantidades por linea contractual
- congelar `estimate_line_snapshots`

### 4. Exportacion
- escribir sobre la plantilla del cliente
- llenar caratula
- crear hojas de memoria por item con sus filas ejecutadas
- preparar anexo de evidencias

## Decision operativa importante
La asignacion al corte se hara al nivel de `field_report_rows`, no al nivel del reporte completo. Asi se evita que una misma ejecucion quede en dos cortes y se conserva la trazabilidad por fila.
