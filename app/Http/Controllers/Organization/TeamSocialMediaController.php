<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Organization\TeamSocialMedia;
use App\Models\Organization\Team;
use App\Models\Core\SocialMediaPlatform;
use Illuminate\Http\Request;

class TeamSocialMediaController extends Controller
{
    // 1. LIST (Con Búsqueda Dinámica)
    public function index(Request $request)
    {
        // Eager Loading: Traemos el Equipo y la Plataforma
        $query = TeamSocialMedia::with(['team', 'platform']);

        // BÚSQUEDA AJAX
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                // Buscar por usuario (handle)
                $q->where('handle', 'like', '%' . $search . '%')
                  // O por nombre del equipo
                  ->orWhereHas('team', function($sq) use ($search) {
                      $sq->where('name', 'like', '%' . $search . '%');
                  })
                  // O por nombre de la plataforma (Twitter, Instagram)
                  ->orWhereHas('platform', function($sq) use ($search) {
                      $sq->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        $socials = $query->orderBy('team_id')->paginate(10);
        $socials->appends(['search' => $request->search]);

        return view('organization.team_social_medias.index', compact('socials'));
    }

    // 2. CREATE FORM
    public function create()
    {
        // Cargar equipos activos y únicos
        $teams = Team::where('is_active', 1)
                     ->orderBy('name')
                     ->get()
                     ->unique('name');

        // Cargar plataformas activas
        $platforms = SocialMediaPlatform::where('is_active', 1)
                                        ->orderBy('name')
                                        ->get();
        
        return view('organization.team_social_medias.create', compact('teams', 'platforms'));
    }

    // 3. STORE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'team_id' => 'required|exists:team,team_id',
            'platform_id' => 'required|exists:social_media_platform,social_media_platform_id',
            'handle' => 'required|max:255', // Ej: @realmadrid
        ]);

        TeamSocialMedia::create($validated);
        return redirect()->route('team-social-medias.index')->with('success', 'Social Media link created successfully.');
    }

    // SHOW DETAILS (NUEVO MÉTODO)
    public function show($id)
    {
        // Cargar relaciones para mostrar nombres
        $social = TeamSocialMedia::with(['team', 'platform'])->findOrFail($id);
        return view('organization.team_social_medias.show', compact('social'));
    }

    // 4. EDIT FORM
    public function edit($id)
    {
        // PK es 'team_social_media_id'
        $social = TeamSocialMedia::findOrFail($id);
        
        $teams = Team::where('is_active', 1)
                     ->orderBy('name')
                     ->get()
                     ->unique('name');

        $platforms = SocialMediaPlatform::where('is_active', 1)
                                        ->orderBy('name')
                                        ->get();

        return view('organization.team_social_medias.edit', compact('social', 'teams', 'platforms'));
    }

    // 5. UPDATE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'team_id' => 'required|exists:team,team_id',
            'platform_id' => 'required|exists:social_media_platform,social_media_platform_id',
            'handle' => 'required|max:255',
        ]);

        $social = TeamSocialMedia::findOrFail($id);
        $social->update($validated);

        return redirect()->route('team-social-medias.index')->with('success', 'Social Media link updated successfully.');
    }

    // 6. DELETE
    public function destroy($id)
    {
        $social = TeamSocialMedia::findOrFail($id);
        $social->delete();
        return redirect()->route('team-social-medias.index')->with('success', 'Social Media link deleted successfully.');
    }
}