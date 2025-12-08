<?php

namespace App\Http\Controllers\MatchDay;

use App\Http\Controllers\Controller;
use App\Models\MatchDay\MatchOfficial;
use App\Models\MatchDay\MatchGame;
use App\Models\People\Referee;
use Illuminate\Http\Request;

class MatchOfficialController extends Controller
{
    // 1. LIST
    public function index(Request $request)
    {
        $query = MatchOfficial::with(['match.homeTeam', 'match.awayTeam', 'referee']);

        // BÚSQUEDA AJAX
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('role', 'like', '%' . $search . '%')
                  ->orWhereHas('referee', function($sq) use ($search) {
                      $sq->where('first_name', 'like', '%' . $search . '%')
                        ->orWhere('last_name', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('match.homeTeam', function($sq) use ($search) {
                      $sq->where('name', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('match.awayTeam', function($sq) use ($search) {
                      $sq->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        $officials = $query->orderBy('match_official_id', 'desc')->paginate(10);
        $officials->appends(['search' => $request->search]);

        return view('match_day.match_officials.index', compact('officials'));
    }

    // 2. CREATE FORM (OPTIMIZADO)
    public function create()
    {
        // YA NO cargamos $matches masivamente. Se cargarán por AJAX.
        // Mantenemos la carga normal de referees (asumiendo que no son miles)
        $referees = Referee::where('is_active', 1)->orderBy('last_name')->get();
        
        return view('match_day.match_officials.create', compact('referees'));
    }

    // 3. STORE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'match_id' => 'required|exists:match,match_id',
            'referee_id' => 'required|exists:referee,referee_id',
            'role' => 'required|max:50',
        ]);

        $exists = MatchOfficial::where('match_id', $request->match_id)
                               ->where('referee_id', $request->referee_id)
                               ->exists();

        if ($exists) {
            return back()->withErrors(['msg' => 'This referee is already assigned to this match.'])->withInput();
        }

        MatchOfficial::create($validated);
        return redirect()->route('match-officials.index')->with('success', 'Official assigned successfully.');
    }

    // 4. SHOW DETAIL
    public function show($id)
    {
        $official = MatchOfficial::with(['match.homeTeam', 'match.awayTeam', 'referee'])->findOrFail($id);
        return view('match_day.match_officials.show', compact('official'));
    }

    // 5. EDIT FORM (OPTIMIZADO)
    public function edit($id)
    {
        // Cargamos relaciones del partido para el pre-llenado del select AJAX
        $official = MatchOfficial::with(['match.homeTeam', 'match.awayTeam'])->findOrFail($id);
        
        // YA NO cargamos la lista masiva de partidos
        $referees = Referee::where('is_active', 1)->orderBy('last_name')->get();

        return view('match_day.match_officials.edit', compact('official', 'referees'));
    }

    // 6. UPDATE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'match_id' => 'required|exists:match,match_id',
            'referee_id' => 'required|exists:referee,referee_id',
            'role' => 'required|max:50',
        ]);

        $official = MatchOfficial::findOrFail($id);
        $official->update($validated);

        return redirect()->route('match-officials.index')->with('success', 'Official assignment updated successfully.');
    }

    // 7. DELETE
    public function destroy($id)
    {
        $official = MatchOfficial::findOrFail($id);
        $official->delete();
        return redirect()->route('match-officials.index')->with('success', 'Official assignment removed successfully.');
    }

    // 8. AJAX PARTIDOS (NUEVO MÉTODO)
    public function searchMatches(Request $request)
    {
        $term = $request->get('q');

        if (empty($term)) {
            return response()->json(['results' => []]);
        }

        $matches = MatchGame::with(['homeTeam', 'awayTeam'])
            ->where(function($query) use ($term) {
                $query->whereHas('homeTeam', function($q) use ($term) {
                    $q->where('name', 'like', '%' . $term . '%');
                })
                ->orWhereHas('awayTeam', function($q) use ($term) {
                    $q->where('name', 'like', '%' . $term . '%');
                });
            })
            ->orderBy('match_date', 'desc')
            ->limit(20)
            ->get();

        $results = $matches->map(function($m) {
            return [
                'id' => $m->match_id,
                'text' => ($m->homeTeam->name ?? '?') . ' vs ' . ($m->awayTeam->name ?? '?') . ' (' . ($m->match_date ? $m->match_date->format('d/m/Y') : 'TBD') . ')'
            ];
        });

        return response()->json(['results' => $results]);
    }
}