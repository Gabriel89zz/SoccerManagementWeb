<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    // Configuración centralizada de los reportes disponibles
    // Mapea un 'slug' (url) -> 'view' (nombre real en BD)
    private $availableReports = [
        'goal_analysis' => [
            'view' => 'view_goal_analysis', 
            'title' => 'Análisis de Goles', 
            'icon' => 'fa-futbol',
            'desc' => 'Desglose detallado de goles por tipo, tiempo y jugador.'
        ],
        'best_prospects' => [
            'view' => 'vw_best_prospects_report', 
            'title' => 'Mejores Prospectos', 
            'icon' => 'fa-star',
            'desc' => 'Jugadores jóvenes con alto rendimiento y potencial.'
        ],
        'coach_effectiveness' => [
            'view' => 'vw_coach_effectiveness_report', 
            'title' => 'Efectividad de Entrenadores', 
            'icon' => 'fa-chalkboard-teacher',
            'desc' => 'Rendimiento de los equipos bajo cada director técnico.'
        ],
        'foreign_players' => [
            'view' => 'vw_foreign_players_report', 
            'title' => 'Jugadores Extranjeros', 
            'icon' => 'fa-globe-americas',
            'desc' => 'Distribución y estadísticas de jugadores internacionales.'
        ],
        'injury_report' => [
            'view' => 'vw_injury_report', 
            'title' => 'Informe de Lesiones', 
            'icon' => 'fa-user-injured',
            'desc' => 'Historial médico, tiempos de recuperación y tipos de lesiones.'
        ],
        'shooting_effectiveness' => [
            'view' => 'vw_shooting_effectiveness_report', 
            'title' => 'Efectividad de Tiros', 
            'icon' => 'fa-bullseye',
            'desc' => 'Relación entre tiros realizados, al arco y goles convertidos.'
        ],
        'squad_age' => [
            'view' => 'vw_squad_age_report', 
            'title' => 'Edad de Plantilla', 
            'icon' => 'fa-birthday-cake',
            'desc' => 'Promedio de edad y distribución generacional por equipo.'
        ],
        'stadium_attendance' => [
            'view' => 'vw_stadium_attendance_report', 
            'title' => 'Asistencia a Estadios', 
            'icon' => 'fa-users',
            'desc' => 'Análisis de concurrencia y ocupación de estadios.'
        ],
        'team_discipline' => [
            'view' => 'vw_team_discipline_report', 
            'title' => 'Disciplina de Equipo', 
            'icon' => 'fa-gavel',
            'desc' => 'Conteo de tarjetas amarillas, rojas y faltas por equipo.'
        ],
        'transfer_balance' => [
            'view' => 'vw_transfer_balance_report', 
            'title' => 'Balance de Transferencias', 
            'icon' => 'fa-money-bill-wave',
            'desc' => 'Gastos vs. Ingresos en el mercado de fichajes.'
        ],
    ];

    /**
     * Muestra el dashboard con las tarjetas de todos los reportes.
     */
    public function index()
    {
        return view('reports.index', ['reports' => $this->availableReports]);
    }

    /**
     * Muestra los datos de un reporte específico en una tabla dinámica.
     */
    public function show(Request $request, $key)
    {
        if (!array_key_exists($key, $this->availableReports)) {
            abort(404, 'Reporte no encontrado');
        }

        $reportConfig = $this->availableReports[$key];
        $viewName = $reportConfig['view'];

        // Consulta base a la vista
        $query = DB::table($viewName);

        // Búsqueda genérica (busca en todas las columnas de texto)
        // Nota: Esto es costoso en vistas grandes, úsalo con precaución o añade índices
        if ($request->filled('search')) {
            $search = $request->search;
            // Obtenemos las columnas para saber dónde buscar
            $columns = Schema::getColumnListing($viewName);
            
            $query->where(function($q) use ($columns, $search) {
                foreach ($columns as $column) {
                    $q->orWhere($column, 'like', '%' . $search . '%');
                }
            });
        }

        // Paginamos los resultados (25 por página para ver más datos)
        $data = $query->paginate(25);
        $data->appends(['search' => $request->search]);

        // Obtenemos las columnas dinámicamente del primer resultado para los encabezados
        $columns = [];
        if ($data->count() > 0) {
            $columns = array_keys((array)$data->first());
        } else {
            $columns = Schema::getColumnListing($viewName);
        }

        return view('reports.show', compact('data', 'columns', 'reportConfig', 'key'));
    }

    /**
     * Exporta el reporte completo a CSV.
     */
    public function export($key)
    {
        if (!array_key_exists($key, $this->availableReports)) {
            abort(404);
        }

        $reportConfig = $this->availableReports[$key];
        $viewName = $reportConfig['view'];

        // Stream para descarga eficiente de archivos grandes
        $response = new StreamedResponse(function() use ($viewName) {
            $handle = fopen('php://output', 'w');

            // 1. Obtener encabezados
            $columns = Schema::getColumnListing($viewName);
            fputcsv($handle, $columns);

            // 2. Procesar datos en chunks para no saturar memoria
            DB::table($viewName)->orderBy($columns[0])->chunk(1000, function($rows) use ($handle) {
                foreach ($rows as $row) {
                    fputcsv($handle, (array)$row);
                }
            });

            fclose($handle);
        });

        $filename = $key . '_' . date('Y-m-d_His') . '.csv';

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

        return $response;
    }
}