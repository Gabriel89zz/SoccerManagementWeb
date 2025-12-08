<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Management\SquadMember;
use App\Models\Management\Squad;
use App\Models\People\Player;
use Illuminate\Http\Request;

class SquadMemberController extends Controller
{
    // 1. LIST
    public function index(Request $request)
    {
        $query = SquadMember::with(['squad.team', 'squad.season', 'player']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('player', function($sq) use ($search) {
                    $sq->where('first_name', 'like', '%' . $search . '%')
                      ->orWhere('last_name', 'like', '%' . $search . '%');
                })
                ->orWhereHas('squad.team', function($sq) use ($search) {
                    $sq->where('name', 'like', '%' . $search . '%');
                })
                ->orWhere('jersey_number', 'like', '%' . $search . '%');
            });
        }

        $members = $query->orderBy('squad_member_id', 'desc')->paginate(10);
        $members->appends(['search' => $request->search]);

        return view('management.squad_members.index', compact('members'));
    }

    // 2. CREATE FORM (OPTIMIZADO)
    public function create()
    {
        // Cargar plantillas activas
        $squads = Squad::with(['team', 'season'])
                       ->where('is_active', 1)
                       ->get()
                       ->sortByDesc('season.name');

        // YA NO cargamos $players masivamente. Se usarán vía AJAX.
        
        return view('management.squad_members.create', compact('squads'));
    }

    // 3. STORE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'squad_id' => 'required|exists:squad,squad_id',
            'player_id' => 'required|exists:player,player_id',
            'jersey_number' => 'nullable|integer|min:1|max:99',
            'join_date' => 'required|date',
            'leave_date' => 'nullable|date|after:join_date',
        ]);

        // Validar duplicados
        $exists = SquadMember::where('squad_id', $request->squad_id)
                             ->where('player_id', $request->player_id)
                             ->exists();

        if ($exists) {
            return back()->withErrors(['msg' => 'This player is already assigned to this squad.'])->withInput();
        }

        // Validar dorsal duplicado
        if ($request->filled('jersey_number')) {
            $jerseyExists = SquadMember::where('squad_id', $request->squad_id)
                                       ->where('jersey_number', $request->jersey_number)
                                       ->exists();
            if ($jerseyExists) {
                return back()->withErrors(['msg' => 'Jersey number already taken in this squad.'])->withInput();
            }
        }

        SquadMember::create($validated);
        return redirect()->route('squad-members.index')->with('success', 'Player assigned to squad successfully.');
    }

    // 4. SHOW DETAIL
    public function show($id)
    {
        $member = SquadMember::with(['squad.team', 'squad.season', 'player'])->findOrFail($id);
        return view('management.squad_members.show', compact('member'));
    }

    // 5. EDIT FORM (OPTIMIZADO)
    public function edit($id)
    {
        // Cargamos la relación 'player' para pre-llenar el input en la vista
        $member = SquadMember::with('player')->findOrFail($id);
        
        $squads = Squad::with(['team', 'season'])
                       ->where('is_active', 1)
                       ->get()
                       ->sortByDesc('season.name');

        // YA NO cargamos la lista masiva de jugadores
        return view('management.squad_members.edit', compact('member', 'squads'));
    }

    // 6. UPDATE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'squad_id' => 'required|exists:squad,squad_id',
            'player_id' => 'required|exists:player,player_id',
            'jersey_number' => 'nullable|integer|min:1|max:99',
            'join_date' => 'required|date',
            'leave_date' => 'nullable|date|after:join_date',
        ]);

        $exists = SquadMember::where('squad_id', $request->squad_id)
                             ->where('player_id', $request->player_id)
                             ->where('squad_member_id', '!=', $id)
                             ->exists();

        if ($exists) {
            return back()->withErrors(['msg' => 'This player is already assigned to this squad.'])->withInput();
        }

        $member = SquadMember::findOrFail($id);
        $member->update($validated);

        return redirect()->route('squad-members.index')->with('success', 'Squad assignment updated successfully.');
    }

    // 7. DELETE
    public function destroy($id)
    {
        $member = SquadMember::findOrFail($id);
        $member->delete();
        return redirect()->route('squad-members.index')->with('success', 'Player removed from squad successfully.');
    }

    // 8. AJAX JUGADORES (NUEVO MÉTODO)
    public function searchPlayers(Request $request)
    {
        $term = $request->get('q');

        if (empty($term)) {
            return response()->json(['results' => []]);
        }

        $players = Player::where('is_active', 1)
                         ->where(function($query) use ($term) {
                             $query->where('first_name', 'like', '%' . $term . '%')
                                   ->orWhere('last_name', 'like', '%' . $term . '%');
                         })
                         ->with('country')
                         ->orderBy('last_name')
                         ->limit(20)
                         ->get();

        $results = $players->map(function($player) {
            return [
                'id' => $player->player_id,
                'text' => $player->full_name . ' (' . ($player->country->name ?? 'N/A') . ')',
                'firstName' => $player->first_name,
                'lastName' => $player->last_name,
                'country' => $player->country->name ?? 'N/A'
            ];
        });

        return response()->json(['results' => $results]);
    }
}