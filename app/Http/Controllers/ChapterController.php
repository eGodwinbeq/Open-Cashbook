<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChapterController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $chapter = Auth::user()->chapters()->create($request->all());

        session(['active_chapter_id' => $chapter->id]);

        return redirect()->route('dashboard')
            ->with('success', 'Chapter created successfully.');
    }

    public function select(Chapter $chapter)
    {
        if ($chapter->user_id !== Auth::id()) {
            abort(403);
        }

        session(['active_chapter_id' => $chapter->id]);

        return redirect()->route('dashboard');
    }

    public function destroy(Chapter $chapter)
    {
        if ($chapter->user_id !== Auth::id()) {
            abort(403);
        }

        $chapter->delete();

        if (session('active_chapter_id') == $chapter->id) {
            session()->forget('active_chapter_id');
        }

        return redirect()->route('dashboard')
            ->with('success', 'Chapter deleted successfully.');
    }
}
