<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\Core\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    // LIST
    public function index(Request $request)
    {
        $query = Country::query();

        // BÚSQUEDA EN SERVIDOR (Busca en TODA la base de datos)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('iso_code', 'like', '%' . $search . '%');
            });
        }

        $countries = $query->orderBy('name')->paginate(15);
        
        // Esto es vital: mantiene el texto de búsqueda en los enlaces de paginación
        $countries->appends(['search' => $request->search]);

        return view('core.countries.index', compact('countries'));
    }

    // CREATE FORM
    public function create()
    {
        return view('core.countries.create');
    }

    // STORE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:100|unique:country,name',
            'iso_code' => 'required|max:3|unique:country,iso_code',
        ]);

        Country::create($validated);
        return redirect()->route('countries.index')->with('success', 'Country created successfully.');
    }

    // EDIT FORM
    public function edit($id)
    {
        $country = Country::findOrFail($id);
        return view('core.countries.edit', compact('country'));
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|max:100|unique:country,name,'.$id.',country_id',
            'iso_code' => 'required|max:3|unique:country,iso_code,'.$id.',country_id',
        ]);

        $country = Country::findOrFail($id);
        $country->update($validated);

        return redirect()->route('countries.index')->with('success', 'Country updated successfully.');
    }

    // DELETE
    public function destroy($id)
    {
        $country = Country::findOrFail($id);
        $country->delete();
        return redirect()->route('countries.index')->with('success', 'Country deleted successfully.');
    }


    public function show($id)
    {
        // Buscamos el país por su ID.
        // Usamos 'withTrashed()' si quieres permitir ver detalles de países eliminados,
        // de lo contrario quita 'withTrashed()'.
        $country = Country::withTrashed()
            ->with(['creator', 'editor', 'destroyer']) // Carga ansiosa (Eager Loading) para optimizar
            ->findOrFail($id);

        return view('core.countries.show', compact('country'));
    }
}