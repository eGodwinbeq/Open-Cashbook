<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Chapter;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $chapters = $user->chapters;

        // Select chapter: from query param, or session, or default to first
        $chapterId = $request->query('chapter_id') ?? session('active_chapter_id');
        $activeChapter = $chapterId ? $chapters->find($chapterId) : $chapters->first();

        if ($activeChapter) {
            session(['active_chapter_id' => $activeChapter->id]);

            $transactions = $activeChapter->transactions()->orderBy('date', 'desc')->paginate(10);
            $totalIn = $activeChapter->transactions()->where('type', 'in')->sum('amount');
            $totalOut = $activeChapter->transactions()->where('type', 'out')->sum('amount');
            $balance = $totalIn - $totalOut;
        } else {
            $transactions = collect();
            $totalIn = 0;
            $totalOut = 0;
            $balance = 0;
        }

        return view('dashboard', compact('chapters', 'activeChapter', 'transactions', 'totalIn', 'totalOut', 'balance'));
    }
}
