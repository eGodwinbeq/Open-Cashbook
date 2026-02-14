<?php

namespace App\Http\Controllers;

use App\Models\Debtor;
use App\Models\Contact;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DebtorController extends Controller
{
    public function index(Request $request)
    {
        $query = auth()->user()->debtors()->with(['contact', 'chapter']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $debtors = $query->orderBy('created_at', 'desc')->paginate(15);
        $totalOutstanding = auth()->user()->debtors()
            ->whereIn('status', ['pending', 'partial'])
            ->sum('balance');

        return view('debtors.index', compact('debtors', 'totalOutstanding'));
    }

    public function create()
    {
        $contacts = auth()->user()->contacts()->orderBy('name')->get();
        $chapters = auth()->user()->chapters;

        return view('debtors.create', compact('contacts', 'chapters'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'contact_id' => 'required|exists:contacts,id',
            'chapter_id' => 'nullable|exists:chapters,id',
            'amount' => 'required|numeric|min:0.01',
            'date_given' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:date_given',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            // Auto-link to Financial chapter if no chapter specified
            if (!isset($validated['chapter_id'])) {
                $financialChapter = auth()->user()->chapters()
                    ->firstOrCreate(
                        ['name' => 'Financial'],
                        ['description' => 'Default financial chapter']
                    );
                $validated['chapter_id'] = $financialChapter->id;
            }

            // Create cash out transaction
            $transaction = Transaction::create([
                'chapter_id' => $validated['chapter_id'],
                'type' => 'out',
                'amount' => $validated['amount'],
                'description' => $validated['description'] ?? 'Loan given',
                'category' => 'Debtor Loan',
                'date' => $validated['date_given'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // Create debtor record
            Debtor::create([
                'user_id' => auth()->id(),
                'contact_id' => $validated['contact_id'],
                'chapter_id' => $validated['chapter_id'],
                'transaction_id' => $transaction->id,
                'amount' => $validated['amount'],
                'paid_amount' => 0,
                'balance' => $validated['amount'],
                'date_given' => $validated['date_given'],
                'due_date' => $validated['due_date'] ?? null,
                'status' => 'pending',
                'description' => $validated['description'],
                'notes' => $validated['notes'],
            ]);
        });

        return redirect()->route('debtors.index')
            ->with('success', 'Debtor record created and cash deducted successfully!');
    }

    public function show(Debtor $debtor)
    {
        $debtor->load(['contact', 'chapter', 'payments.transaction']);

        return view('debtors.show', compact('debtor'));
    }

    public function addPayment(Request $request, Debtor $debtor)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $debtor->balance,
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,bank_transfer,check,mobile_money,other',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($debtor, $validated) {
            // Create cash in transaction
            $transaction = Transaction::create([
                'chapter_id' => $debtor->chapter_id,
                'type' => 'in',
                'amount' => $validated['amount'],
                'description' => "Payment from {$debtor->contact->name}",
                'category' => 'Debtor Payment',
                'date' => $validated['payment_date'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // Add payment to debtor
            $debtor->addPayment([
                'transaction_id' => $transaction->id,
                'amount' => $validated['amount'],
                'payment_date' => $validated['payment_date'],
                'payment_method' => $validated['payment_method'],
                'reference_number' => $validated['reference_number'],
                'notes' => $validated['notes'],
            ]);
        });

        return redirect()->route('debtors.show', $debtor)
            ->with('success', 'Payment recorded successfully!');
    }

    public function destroy(Debtor $debtor)
    {
        $debtor->delete();

        return redirect()->route('debtors.index')
            ->with('success', 'Debtor record deleted successfully!');
    }
}
