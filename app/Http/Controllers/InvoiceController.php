<?php
namespace App\Http\Controllers;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Chapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = auth()->user()->invoices()->with(['chapter', 'items']);
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('client_name', 'like', "%{$search}%");
            });
        }
        $invoices = $query->orderBy('created_at', 'desc')->paginate(15);
        $chapters = auth()->user()->chapters;
        return view('invoices.index', compact('invoices', 'chapters'));
    }
    public function create()
    {
        $chapters = auth()->user()->chapters;
        $invoiceNumber = Invoice::generateInvoiceNumber();
        return view('invoices.create', compact('chapters', 'invoiceNumber'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'chapter_id' => 'nullable|exists:chapters,id',
            'client_name' => 'required|string|max:255',
            'client_email' => 'nullable|email|max:255',
            'client_address' => 'nullable|string',
            'client_phone' => 'nullable|string|max:50',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'terms' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);
        DB::transaction(function () use ($validated) {
            $invoice = Invoice::create([
                'user_id' => auth()->id(),
                'chapter_id' => $validated['chapter_id'] ?? null,
                'invoice_number' => Invoice::generateInvoiceNumber(),
                'client_name' => $validated['client_name'],
                'client_email' => $validated['client_email'] ?? null,
                'client_address' => $validated['client_address'] ?? null,
                'client_phone' => $validated['client_phone'] ?? null,
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'],
                'tax_rate' => $validated['tax_rate'] ?? 0,
                'discount_amount' => $validated['discount_amount'] ?? 0,
                'notes' => $validated['notes'] ?? null,
                'terms' => $validated['terms'] ?? null,
                'status' => 'draft',
            ]);
            foreach ($validated['items'] as $index => $item) {
                $amount = $item['quantity'] * $item['unit_price'];
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'amount' => $amount,
                    'sort_order' => $index,
                ]);
            }
            $invoice->calculateTotals();
        });
        return redirect()->route('invoices.index')->with('success', 'Invoice created successfully!');
    }
    public function show(Invoice $invoice)
    {
        if ($invoice->user_id !== auth()->id()) {
            abort(403);
        }
        $invoice->load(['items', 'chapter']);
        return view('invoices.show', compact('invoice'));
    }
    public function edit(Invoice $invoice)
    {
        if ($invoice->user_id !== auth()->id()) {
            abort(403);
        }
        $chapters = auth()->user()->chapters;
        $invoice->load('items');
        return view('invoices.edit', compact('invoice', 'chapters'));
    }
    public function update(Request $request, Invoice $invoice)
    {
        if ($invoice->user_id !== auth()->id()) {
            abort(403);
        }
        $validated = $request->validate([
            'chapter_id' => 'nullable|exists:chapters,id',
            'client_name' => 'required|string|max:255',
            'client_email' => 'nullable|email|max:255',
            'client_address' => 'nullable|string',
            'client_phone' => 'nullable|string|max:50',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'terms' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);
        DB::transaction(function () use ($invoice, $validated) {
            $invoice->update([
                'chapter_id' => $validated['chapter_id'] ?? null,
                'client_name' => $validated['client_name'],
                'client_email' => $validated['client_email'] ?? null,
                'client_address' => $validated['client_address'] ?? null,
                'client_phone' => $validated['client_phone'] ?? null,
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'],
                'tax_rate' => $validated['tax_rate'] ?? 0,
                'discount_amount' => $validated['discount_amount'] ?? 0,
                'notes' => $validated['notes'] ?? null,
                'terms' => $validated['terms'] ?? null,
            ]);
            $invoice->items()->delete();
            foreach ($validated['items'] as $index => $item) {
                $amount = $item['quantity'] * $item['unit_price'];
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'amount' => $amount,
                    'sort_order' => $index,
                ]);
            }
            $invoice->calculateTotals();
        });
        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice updated successfully!');
    }
    public function destroy(Invoice $invoice)
    {
        if ($invoice->user_id !== auth()->id()) {
            abort(403);
        }
        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', 'Invoice deleted successfully!');
    }
    public function updateStatus(Request $request, Invoice $invoice)
    {
        if ($invoice->user_id !== auth()->id()) {
            abort(403);
        }
        $validated = $request->validate([
            'status' => 'required|in:draft,sent,paid,overdue,cancelled'
        ]);
        $invoice->status = $validated['status'];
        if ($validated['status'] === 'paid') {
            $invoice->paid_date = now();
        }
        $invoice->save();
        return back()->with('success', 'Invoice status updated successfully!');
    }
    public function markAsPaid(Invoice $invoice)
    {
        if ($invoice->user_id !== auth()->id()) {
            abort(403);
        }
        $invoice->markAsPaid();
        return back()->with('success', 'Invoice marked as paid!');
    }
    public function download(Invoice $invoice)
    {
        if ($invoice->user_id !== auth()->id()) {
            abort(403);
        }
        $invoice->load('items');
        return view('invoices.pdf', compact('invoice'));
    }
}
