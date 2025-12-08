<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Organization\Team;
use App\Models\People\Player;
use App\Models\MatchDay\MatchGame;
use App\Models\Management\Injury;
use App\Models\Management\TransferHistory; 
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. KPI Cards (Conteos rápidos)
        $counts = [
            'teams' => Team::where('is_active', 1)->count(),
            'players' => Player::where('is_active', 1)->count(),
            'matches_scheduled' => MatchGame::where('match_status', 'Scheduled')->count(),
            'active_injuries' => Injury::whereNull('actual_return_date')->count(),
        ];

        // 2. Datos para Gráficos
        $playersByPosition = DB::table('player')
            ->join('position', 'player.primary_position_id', '=', 'position.position_id')
            ->select('position.acronym', DB::raw('count(*) as total'))
            ->groupBy('position.acronym')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $matchesByStatus = DB::table('match')
            ->select('match_status', DB::raw('count(*) as total'))
            ->groupBy('match_status')
            ->get();

        // 3. Widget "Últimas Transferencias" (Reemplaza a Usuarios)
        // Traemos las últimas 5 transferencias con los datos del jugador y equipos
        $latestTransfers = TransferHistory::with(['player', 'fromTeam', 'toTeam'])
                           ->orderBy('transfer_date', 'desc')
                           ->take(5)
                           ->get();

        // 4. Próximos Partidos
        $upcomingMatches = MatchGame::with(['homeTeam', 'awayTeam'])
                                    ->where('match_status', 'Scheduled')
                                    ->where('match_date', '>=', now())
                                    ->orderBy('match_date', 'asc')
                                    ->take(4)
                                    ->get();

        return view('dashboard', compact(
            'counts', 
            'playersByPosition', 
            'matchesByStatus', 
            'latestTransfers', // <--- Variable nueva
            'upcomingMatches'
        ));
    }
}