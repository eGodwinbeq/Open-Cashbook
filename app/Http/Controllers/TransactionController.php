<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $chapterId = session('active_chapter_id');
        $activeChapter = $chapterId ? auth()->user()->chapters()->find($chapterId) : auth()->user()->chapters()->first();

        if (!$activeChapter) {
            return redirect()->route('dashboard')->with('error', 'Please create a chapter first.');
        }

        $query = $activeChapter->transactions();

        // Filtering
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('category')) {
            $query->where('category', 'like', '%' . $request->category . '%');
        }

        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        $transactions = $query->orderBy('date', 'desc')->paginate(20)->withQueryString();

        $chapters = auth()->user()->chapters;

        return view('transactions.index', compact('transactions', 'activeChapter', 'chapters'));
    }

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
