<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\Core\SocialMediaPlatform;
use Illuminate\Http\Request;

class SocialMediaPlatformController extends Controller
{
    // LIST
    public function index()
    {
        $platforms = SocialMediaPlatform::orderBy('name')->paginate(15);
        // OJO: Ajustado a snake_case para coincidir con tu carpeta de vistas
        return view('core.social_media_platforms.index', compact('platforms'));
    }

    // CREATE FORM
    public function create()
    {
        return view('core.social_media_platforms.create');
    }

    // STORE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:100|unique:social_media_platform,name',
        ]);

        SocialMediaPlatform::create($validated);
        return redirect()->route('social-media-platforms.index')->with('success', 'Platform created successfully.');
    }

    // SHOW DETAILS (NUEVO MÉTODO)
    public function show($id)
    {
        $platform = SocialMediaPlatform::findOrFail($id);
        return view('core.social_media_platforms.show', compact('platform'));
    }

    // EDIT FORM
    public function edit($id)
    {
        $platform = SocialMediaPlatform::findOrFail($id);
        return view('core.social_media_platforms.edit', compact('platform'));
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|max:100|unique:social_media_platform,name,'.$id.',social_media_platform_id',
        ]);

        $platform = SocialMediaPlatform::findOrFail($id);
        $platform->update($validated);

        return redirect()->route('social-media-platforms.index')->with('success', 'Platform updated successfully.');
    }

    // DELETE
    public function destroy($id)
    {
        $platform = SocialMediaPlatform::findOrFail($id);
        $platform->delete();
        return redirect()->route('social-media-platforms.index')->with('success', 'Platform deleted successfully.');
    }
}