<?php

namespace App\Http\Controllers\People;

use App\Http\Controllers\Controller;
use App\Models\People\Player;
use App\Models\Core\Country;
use App\Models\Core\Position;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    // 1. LIST
    public function index(Request $request)
    {
        $query = Player::with(['country', 'position']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', '%' . $search . '%')
                  ->orWhere('last_name', 'like', '%' . $search . '%')
                  ->orWhereHas('position', function($sq) use ($search) {
                      $sq->where('name', 'like', '%' . $search . '%')
                         ->orWhere('acronym', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('country', function($sq) use ($search) {
                      $sq->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        $players = $query->orderBy('last_name')->paginate(10);
        $players->appends(['search' => $request->search]);

        return view('people.players.index', compact('players'));
    }

    // 2. CREATE FORM
    public function create()
    {
        $countries = Country::where('is_active', 1)->orderBy('name')->get();
        $positions = Position::where('is_active', 1)->orderBy('name')->get();
        
        return view('people.players.create', compact('countries', 'positions'));
    }

    // 3. STORE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|max:100',
            'last_name' => 'required|max:100',
            'date_of_birth' => 'required|date',
            'country_id' => 'required|exists:country,country_id',
            'primary_position_id' => 'nullable|exists:position,position_id',
            'height' => 'nullable|numeric|min:0|max:300',
            'weight' => 'nullable|numeric|min:0|max:200',
            'preferred_foot' => 'required|in:Left,Right,Both',
        ]);

        Player::create($validated);
        return redirect()->route('players.index')->with('success', 'Player created successfully.');
    }

    // 4. SHOW DETAIL (NUEVO)
    public function show($id)
    {
        // Cargamos relaciones para mostrar nombres en lugar de IDs
        $player = Player::with(['country', 'position'])->findOrFail($id);
        return view('people.players.show', compact('player'));
    }

    // 5. EDIT FORM
    public function edit($id)
    {
        $player = Player::findOrFail($id);
        $countries = Country::where('is_active', 1)->orderBy('name')->get();
        $positions = Position::where('is_active', 1)->orderBy('name')->get();

        return view('people.players.edit', compact('player', 'countries', 'positions'));
    }

    // 6. UPDATE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'first_name' => 'required|max:100',
            'last_name' => 'required|max:100',
            'date_of_birth' => 'required|date',
            'country_id' => 'required|exists:country,country_id',
            'primary_position_id' => 'nullable|exists:position,position_id',
            'height' => 'nullable|numeric|min:0|max:300',
            'weight' => 'nullable|numeric|min:0|max:200',
            'preferred_foot' => 'required|in:Left,Right,Both',
        ]);

        $player = Player::findOrFail($id);
        $player->update($validated);

        return redirect()->route('players.index')->with('success', 'Player updated successfully.');
    }

    // 7. DELETE
    public function destroy($id)
    {
        $player = Player::findOrFail($id);
        $player->delete();
        return redirect()->route('players.index')->with('success', 'Player deleted successfully.');
    }
}