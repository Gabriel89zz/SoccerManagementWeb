<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\Core\City;
use App\Models\Core\Country;
use Illuminate\Http\Request;

class CityController extends Controller
{
    // LIST
    public function index(Request $request)
    {
        // Eager loading de 'country' para optimizar
        $query = City::with('country');

        // BÚSQUEDA AJAX EN SERVIDOR
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    // Buscamos también dentro de la relación 'country'
                    ->orWhereHas('country', function ($sq) use ($search) {
                        $sq->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        $cities = $query->orderBy('name')->paginate(15);
        $cities->appends(['search' => $request->search]);

        return view('core.cities.index', compact('cities'));
    }

    // CREATE FORM
    public function create()
    {
        $countries = Country::where('is_active', 1)->orderBy('name')->get();
        return view('core.cities.create', compact('countries'));
    }

    // STORE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:100',
            'country_id' => 'required|exists:country,country_id',
        ]);

        City::create($validated);
        return redirect()->route('cities.index')->with('success', 'City created successfully.');
    }

    public function show($id)
    {
        $city = City::with('country')->findOrFail($id);
        return view('core.cities.show', compact('city'));
    }

    // EDIT FORM
    public function edit($id)
    {
        $city = City::findOrFail($id);
        $countries = Country::where('is_active', 1)->orderBy('name')->get();
        return view('core.cities.edit', compact('city', 'countries'));
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|max:100',
            'country_id' => 'required|exists:country,country_id',
        ]);

        $city = City::findOrFail($id);
        $city->update($validated);

        return redirect()->route('cities.index')->with('success', 'City updated successfully.');
    }

    // DELETE
    public function destroy($id)
    {
        $city = City::findOrFail($id);
        $city->delete();
        return redirect()->route('cities.index')->with('success', 'City deleted successfully.');
    }


    public function search(Request $request)
    {
        $term = $request->get('q');

        if (empty($term)) {
            return response()->json(['results' => []]);
        }

        $cities = City::where('name', 'like', '%' . $term . '%')
            ->with('country') // Traemos el país para mostrarlo
            ->limit(20)       // Límite para no saturar
            ->get();

        // Formateamos para Select2
        $results = $cities->map(function ($city) {
            return [
                'id' => $city->city_id,
                'text' => $city->name . ' (' . ($city->country->name ?? 'N/A') . ')'
            ];
        });

        return response()->json(['results' => $results]);
    }
}