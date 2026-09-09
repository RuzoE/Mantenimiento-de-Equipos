<?php

namespace App\Support;

use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Genera descargas CSV compatibles con Excel (BOM UTF-8 y separador ";").
 * Se usa en lugar de un escritor .xlsx porque maatwebsite/excel aún no soporta
 * Laravel 13 y la extensión ext-zip no está disponible en este entorno.
 */
class ExportadorCsv
{
    /**
     * @param  array<int, string>  $encabezados
     * @param  iterable<int, array<int, mixed>>  $filas
     */
    public static function descargar(string $nombreArchivo, array $encabezados, iterable $filas): StreamedResponse
    {
        return response()->streamDownload(function () use ($encabezados, $filas) {
            $salida = fopen('php://output', 'wb');
            fwrite($salida, "\xEF\xBB\xBF"); // BOM UTF-8: Excel abre acentos correctamente
            fputcsv($salida, $encabezados, ';');

            foreach ($filas as $fila) {
                fputcsv($salida, $fila, ';');
            }

            fclose($salida);
        }, $nombreArchivo, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
