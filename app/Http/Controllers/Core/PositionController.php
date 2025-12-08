<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\Core\Position;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function index()
    {
        $positions = Position::orderBy('name')->paginate(15);
        return view('core.positions.index', compact('positions'));
    }

    public function create()
    {
        return view('core.positions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:50|unique:position,name',
            'acronym' => 'required|max:5|unique:position,acronym',
            'category' => 'required|max:20', // Goalkeeper, Defender, etc.
        ]);

        Position::create($validated);
        return redirect()->route('positions.index')->with('success', 'Position created successfully.');
    }

    // SHOW DETAILS (NUEVO MÉTODO)
    public function show($id)
    {
        $position = Position::findOrFail($id);
        return view('core.positions.show', compact('position'));
    }

    public function edit($id)
    {
        $position = Position::findOrFail($id);
        return view('core.positions.edit', compact('position'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|max:50|unique:position,name,'.$id.',position_id',
            'acronym' => 'required|max:5|unique:position,acronym,'.$id.',position_id',
            'category' => 'required|max:20',
        ]);

        $position = Position::findOrFail($id);
        $position->update($validated);

        return redirect()->route('positions.index')->with('success', 'Position updated successfully.');
    }

    public function destroy($id)
    {
        $position = Position::findOrFail($id);
        $position->delete();
        return redirect()->route('positions.index')->with('success', 'Position deleted successfully.');
    }
}