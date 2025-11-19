<?php

namespace App\Http\Controllers\Api;

use App\Models\Artikel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Illuminate\Support\Str;

class ArtikelController extends Controller
{
    public function index()
    {
        $artikels = Artikel::latest()->get();
        return response()->json($artikels);
    }

    public function adminIndex()
    {
        $artikels = Artikel::latest()->get();
        return Inertia::render('Dashboard/Artikel/Index', [
            'artikels' => $artikels
        ]);
    }

    public function create()
    {
        return Inertia::render('Dashboard/Artikel/Create');
    }

    public function show($slug)
    {
        $artikel = Artikel::where('slug', $slug)->firstOrFail();
        return response()->json($artikel);
    }

    public function adminShow($slug)
    {
        $artikel = Artikel::where('slug', $slug)->firstOrFail();
        return Inertia::render('Dashboard/Artikel/Show', [
            'artikel' => $artikel
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|min:10|max:60',
            'excerpt' => 'required|string',
            'image' => 'required|url',
            'date' => 'required|date|after_or_equal:-5 days|before_or_equal:today',
        ]);

        $slug = Str::slug($request->title);
        $originalSlug = $slug;
        $count = 1;

        while (Artikel::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        $artikel = Artikel::create([
            'title' => $request->title,
            'slug' => $slug,
            'excerpt' => $request->excerpt,
            'image' => $request->image,
            'date' => $request->date,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Artikel berhasil dibuat.',
                'data' => $artikel,
            ]);
        }

        return redirect()->route('dashboard.artikel.index')
            ->with('message', 'Artikel berhasil dibuat.');
    }

    public function edit($slug)
    {
        $artikel = Artikel::where('slug', $slug)->firstOrFail();
        return Inertia::render('Dashboard/Artikel/Edit', [
            'artikel' => $artikel
        ]);
    }

    public function update(Request $request, $slug)
    {
        $artikel = Artikel::where('slug', $slug)->firstOrFail();

        $request->validate([
            'title' => 'required|string|min:10|max:60',
            'excerpt' => 'required|string',
            'image' => 'required|url',
            'date' => 'required|date|after_or_equal:-5 days|before_or_equal:today',
        ]);

        if ($request->title !== $artikel->title) {
            $newSlug = Str::slug($request->title);
            $originalSlug = $newSlug;
            $count = 1;

            while (Artikel::where('slug', $newSlug)
                         ->where('id', '!=', $artikel->id)
                         ->exists()) {
                $newSlug = $originalSlug . '-' . $count;
                $count++;
            }

            $artikel->slug = $newSlug;
        }

        $artikel->title = $request->title;
        $artikel->excerpt = $request->excerpt;
        $artikel->image = $request->image;
        $artikel->date = $request->date;
        $artikel->save();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Artikel berhasil diperbarui.',
                'data' => $artikel,
            ]);
        }

        return redirect()->route('dashboard.artikel.index')
            ->with('message', 'Artikel berhasil diperbarui.');
    }

    public function destroy($slug)
    {
        $artikel = Artikel::where('slug', $slug)->firstOrFail();
        $artikel->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'message' => 'Artikel berhasil dihapus.'
            ]);
        }

        return redirect()->route('dashboard.artikel.index')
            ->with('message', 'Artikel berhasil dihapus.');
    }

    public function publicIndex()
    {
        $artikels = Artikel::latest()->paginate(9);
        return Inertia::render('Artikel/Artikel', [
            'artikels' => $artikels
        ]);
    }

    public function publicShow($slug)
    {
        $artikel = Artikel::where('slug', $slug)->firstOrFail();
        return Inertia::render('Artikel/ArtikelDetail', [
            'artikel' => $artikel
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        $artikels = Artikel::where('title', 'LIKE', "%{$query}%")
            ->orWhere('excerpt', 'LIKE', "%{$query}%")
            ->latest()
            ->get();

        if ($request->wantsJson()) {
            return response()->json($artikels);
        }

        return Inertia::render('Dashboard/Artikel/Index', [
            'artikels' => $artikels,
            'searchQuery' => $query
        ]);
    }
}
