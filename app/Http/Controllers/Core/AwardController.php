<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\Core\Award;
use Illuminate\Http\Request;

class AwardController extends Controller
{
    // 1. LIST
    public function index(Request $request)
    {
        $query = Award::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('scope', 'like', '%' . $search . '%');
            });
        }

        $awards = $query->orderBy('name')->paginate(15);
        $awards->appends(['search' => $request->search]);

        return view('core.awards.index', compact('awards'));
    }

    // 2. CREATE FORM
    public function create()
    {
        return view('core.awards.create');
    }

    // 3. STORE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:100|unique:award,name',
            'scope' => 'required|max:50',
        ]);

        Award::create($validated);
        return redirect()->route('awards.index')->with('success', 'Award created successfully.');
    }

    // 4. SHOW DETAIL (NUEVO)
    public function show($id)
    {
        $award = Award::findOrFail($id);
        return view('core.awards.show', compact('award'));
    }

    // 5. EDIT FORM
    public function edit($id)
    {
        $award = Award::findOrFail($id);
        return view('core.awards.edit', compact('award'));
    }

    // 6. UPDATE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|max:100|unique:award,name,'.$id.',award_id',
            'scope' => 'required|max:50',
        ]);

        $award = Award::findOrFail($id);
        $award->update($validated);

        return redirect()->route('awards.index')->with('success', 'Award updated successfully.');
    }

    // 7. DELETE
    public function destroy($id)
    {
        $award = Award::findOrFail($id);
        $award->delete();
        return redirect()->route('awards.index')->with('success', 'Award deleted successfully.');
    }
}