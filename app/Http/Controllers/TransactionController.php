<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function store(Request $request, Chapter $chapter)
    {
        // Simple authorization: check if chapter belongs to user
        if ($chapter->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'type' => 'required|in:in,out',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $chapter->transactions()->create($request->all());

        return back()->with('success', 'Transaction added successfully.');
    }
}
