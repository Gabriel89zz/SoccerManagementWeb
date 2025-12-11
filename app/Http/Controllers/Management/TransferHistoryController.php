<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Management\TransferHistory;
use App\Models\People\Player;
use App\Models\Organization\Team;
use Illuminate\Http\Request;

class TransferHistoryController extends Controller
{
    // 1. LIST
    public function index(Request $request)
    {
        $query = TransferHistory::with(['player', 'fromTeam', 'toTeam']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('player', function ($sq) use ($search) {
                    $sq->where('first_name', 'like', '%' . $search . '%')
                        ->orWhere('last_name', 'like', '%' . $search . '%');
                })
                    ->orWhereHas('fromTeam', function ($sq) use ($search) {
                        $sq->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('toTeam', function ($sq) use ($search) {
                        $sq->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        $transfers = $query->orderBy('transfer_date', 'desc')->paginate(10);
        $transfers->appends(['search' => $request->search]);

        return view('management.transfer_histories.index', compact('transfers'));
    }

    // 2. CREATE FORM
    public function create()
    {
        return view('management.transfer_histories.create');
    }

    // 3. STORE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'player_id' => 'required|exists:player,player_id',
            'transfer_date' => 'required|date',
            'transfer_type' => 'required|in:Transfer,Loan,Free Agent',
            'from_team_id' => 'nullable|exists:team,team_id',
            'to_team_id' => 'required|exists:team,team_id|different:from_team_id',
            'transfer_fee_eur' => 'required|numeric|min:0',
        ]);

        TransferHistory::create($validated);
        return redirect()->route('transfer-histories.index')->with('success', 'Transfer recorded successfully.');
    }

    // 4. SHOW DETAIL
    public function show($id)
    {
        $transfer = TransferHistory::with(['player', 'fromTeam', 'toTeam'])->findOrFail($id);
        return view('management.transfer_histories.show', compact('transfer'));
    }

    // 5. EDIT FORM
    public function edit($id)
    {
        $transfer = TransferHistory::with(['player', 'fromTeam', 'toTeam'])->findOrFail($id);

        return view('management.transfer_histories.edit', compact('transfer'));
    }

    // 6. UPDATE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'player_id' => 'required|exists:player,player_id',
            'transfer_date' => 'required|date',
            'transfer_type' => 'required|in:Transfer,Loan,Free Agent',
            'from_team_id' => 'nullable|exists:team,team_id',
            'to_team_id' => 'required|exists:team,team_id|different:from_team_id',
            'transfer_fee_eur' => 'required|numeric|min:0',
        ]);

        $transfer = TransferHistory::findOrFail($id);
        $transfer->update($validated);

        return redirect()->route('transfer-histories.index')->with('success', 'Transfer updated successfully.');
    }

    // 7. DELETE
    public function destroy($id)
    {
        $transfer = TransferHistory::findOrFail($id);
        $transfer->delete();
        return redirect()->route('transfer-histories.index')->with('success', 'Transfer deleted successfully.');
    }

    public function searchPlayers(Request $request)
    {
        $term = $request->get('q');
        if (empty($term))
            return response()->json(['results' => []]);

        $players = Player::where('is_active', 1)
            ->where(function ($query) use ($term) {
                $query->where('first_name', 'like', '%' . $term . '%')
                    ->orWhere('last_name', 'like', '%' . $term . '%');
            })
            ->with('country')
            ->orderBy('last_name')
            ->limit(20)
            ->get();

        $results = $players->map(function ($player) {
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

    public function searchTeams(Request $request)
    {
        $term = $request->get('q');
        if (empty($term))
            return response()->json(['results' => []]);

        $teams = Team::where('is_active', 1)
            ->where('name', 'like', '%' . $term . '%')
            ->orderBy('name')
            ->limit(20)
            ->get();

        $results = $teams->map(function ($team) {
            return [
                'id' => $team->team_id,
                'text' => $team->name
            ];
        });

        return response()->json(['results' => $results]);
    }
}