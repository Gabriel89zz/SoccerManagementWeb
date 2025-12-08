<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\Core\EventType;
use Illuminate\Http\Request;

class EventTypeController extends Controller
{
    // LIST
    public function index(Request $request)
    {
        $query = EventType::query();

        // BÚSQUEDA AJAX EN SERVIDOR
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', '%' . $search . '%');
        }

        $eventTypes = $query->orderBy('name')->paginate(15);
        $eventTypes->appends(['search' => $request->search]);

        return view('core.event_types.index', compact('eventTypes'));
    }

    // CREATE FORM
    public function create()
    {
        return view('core.event_types.create');
    }

    // STORE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:50|unique:event_type,name',
        ]);

        EventType::create($validated);
        return redirect()->route('event-types.index')->with('success', 'Event Type created successfully.');
    }

    // SHOW DETAILS (NUEVO MÉTODO)
    public function show($id)
    {
        $eventType = EventType::findOrFail($id);
        return view('core.event_types.show', compact('eventType'));
    }

    // EDIT FORM
    public function edit($id)
    {
        $eventType = EventType::findOrFail($id);
        return view('core.event_types.edit', compact('eventType'));
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|max:50|unique:event_type,name,'.$id.',event_type_id',
        ]);

        $eventType = EventType::findOrFail($id);
        $eventType->update($validated);

        return redirect()->route('event-types.index')->with('success', 'Event Type updated successfully.');
    }

    // DELETE
    public function destroy($id)
    {
        $eventType = EventType::findOrFail($id);
        $eventType->delete();
        return redirect()->route('event-types.index')->with('success', 'Event Type deleted successfully.');
    }
}