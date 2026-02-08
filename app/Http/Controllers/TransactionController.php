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

    public function store(Request $request, Chapter $chapter = null)
    {
        // If no chapter provided via route parameter, get from session
        // If chapter provided but doesn't belong to user, also use session
        if (!$chapter || $chapter->user_id !== auth()->id()) {
            $chapterId = session('active_chapter_id');
            $chapter = $chapterId ? auth()->user()->chapters()->find($chapterId) : auth()->user()->chapters()->first();

            if (!$chapter) {
                return back()->with('error', 'Please create or select a chapter first.');
            }
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
