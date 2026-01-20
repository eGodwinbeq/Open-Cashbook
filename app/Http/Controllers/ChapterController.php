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

        return redirect()->route('dashboard', ['chapter_id' => $chapter->id])
            ->with('success', 'Chapter created successfully.');
    }
}
