<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Chapter;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoicePayment;
use App\Models\Receipt;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceSoftDeleteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that deleted invoices don't appear in invoice list
     */
    public function test_deleted_invoices_dont_appear_in_list(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        // Create two invoices
        $invoice1 = Invoice::create([
            'user_id' => $user->id,
            'invoice_number' => 'INV-TEST-001',
            'client_name' => 'Test Client 1',
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'status' => 'draft',
            'total_amount' => 100.00,
        ]);

        $invoice2 = Invoice::create([
            'user_id' => $user->id,
            'invoice_number' => 'INV-TEST-002',
            'client_name' => 'Test Client 2',
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'status' => 'draft',
            'total_amount' => 200.00,
        ]);

        // Delete first invoice
        $invoice1->delete();

        // Check invoice list
        $response = $this->actingAs($user)->get(route('invoices.index'));

        // Should see invoice 2 but not invoice 1
        $response->assertSee('INV-TEST-002');
        $response->assertDontSee('INV-TEST-001');

        // Verify database
        $this->assertSoftDeleted('invoices', ['id' => $invoice1->id]);
        $this->assertNotSoftDeleted('invoices', ['id' => $invoice2->id]);
    }

    /**
     * Test that deleted invoices don't appear in revenue report
     */
    public function test_deleted_invoices_dont_appear_in_revenue_report(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        // Create outstanding invoice
        $outstandingInvoice = Invoice::create([
            'user_id' => $user->id,
            'invoice_number' => 'INV-OUTSTANDING',
            'client_name' => 'Outstanding Client',
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'status' => 'sent',
            'total_amount' => 500.00,
            'balance_due' => 500.00,
        ]);

        // Create paid invoice
        $paidInvoice = Invoice::create([
            'user_id' => $user->id,
            'invoice_number' => 'INV-PAID',
            'client_name' => 'Paid Client',
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'status' => 'paid',
            'total_amount' => 1000.00,
            'paid_amount' => 1000.00,
            'balance_due' => 0,
        ]);

        // View revenue report - should see both
        $response = $this->actingAs($user)->get(route('revenue.report'));
        $response->assertSee('INV-OUTSTANDING');
        $response->assertSee('INV-PAID');
        $response->assertSee('500'); // Outstanding amount
        $response->assertSee('1,000'); // Paid amount

        // Delete the paid invoice
        $paidInvoice->delete();

        // View revenue report again - should not see deleted invoice
        $response = $this->actingAs($user)->get(route('revenue.report'));
        $response->assertSee('INV-OUTSTANDING');
        $response->assertDontSee('INV-PAID');
        $response->assertSee('500'); // Still see outstanding
        $response->assertDontSee('1,000'); // Don't see deleted paid amount
    }

    /**
     * Test that deleting invoice also soft-deletes related items
     */
    public function test_deleting_invoice_soft_deletes_items(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $invoice = Invoice::create([
            'user_id' => $user->id,
            'invoice_number' => 'INV-TEST-003',
            'client_name' => 'Test Client',
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'status' => 'draft',
            'total_amount' => 300.00,
        ]);

        // Add items
        $item1 = InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => 'Item 1',
            'quantity' => 1,
            'unit_price' => 100.00,
            'amount' => 100.00,
            'sort_order' => 0,
        ]);

        $item2 = InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => 'Item 2',
            'quantity' => 2,
            'unit_price' => 100.00,
            'amount' => 200.00,
            'sort_order' => 1,
        ]);

        // Delete invoice
        $invoice->delete();

        // Verify items are soft-deleted
        $this->assertSoftDeleted('invoice_items', ['id' => $item1->id]);
        $this->assertSoftDeleted('invoice_items', ['id' => $item2->id]);
    }

    /**
     * Test that deleting invoice also soft-deletes payments and receipts
     */
    public function test_deleting_invoice_soft_deletes_payments_and_receipts(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $chapter = Chapter::create([
            'user_id' => $user->id,
            'name' => 'Financial',
        ]);

        $invoice = Invoice::create([
            'user_id' => $user->id,
            'chapter_id' => $chapter->id,
            'invoice_number' => 'INV-TEST-004',
            'client_name' => 'Test Client',
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'status' => 'sent',
            'total_amount' => 500.00,
            'balance_due' => 500.00,
        ]);

        // Add payment
        $payment = $invoice->addPayment([
            'amount' => 250.00,
            'payment_method' => 'cash',
            'payment_date' => now()->format('Y-m-d'),
            'notes' => 'Test payment',
        ]);

        // Get the transaction and receipt
        $transactionId = $payment->transaction_id;
        $receipt = $payment->receipt;

        // Verify they exist
        $this->assertNotNull($transactionId);
        $this->assertNotNull($receipt);

        // Delete invoice
        $invoice->delete();

        // Verify payment, receipt, and transaction are soft-deleted
        $this->assertSoftDeleted('invoice_payments', ['id' => $payment->id]);
        $this->assertSoftDeleted('receipts', ['id' => $receipt->id]);
        $this->assertSoftDeleted('transactions', ['id' => $transactionId]);
    }

    /**
     * Test that restoring invoice also restores related data
     */
    public function test_restoring_invoice_restores_related_data(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $chapter = Chapter::create([
            'user_id' => $user->id,
            'name' => 'Financial',
        ]);

        $invoice = Invoice::create([
            'user_id' => $user->id,
            'chapter_id' => $chapter->id,
            'invoice_number' => 'INV-TEST-005',
            'client_name' => 'Test Client',
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'status' => 'sent',
            'total_amount' => 500.00,
            'balance_due' => 500.00,
        ]);

        // Add item
        $item = InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => 'Test Item',
            'quantity' => 1,
            'unit_price' => 500.00,
            'amount' => 500.00,
            'sort_order' => 0,
        ]);

        // Add payment
        $payment = $invoice->addPayment([
            'amount' => 500.00,
            'payment_method' => 'cash',
            'payment_date' => now()->format('Y-m-d'),
            'notes' => 'Full payment',
        ]);

        $transactionId = $payment->transaction_id;
        $receiptId = $payment->receipt->id;

        // Delete invoice (soft delete)
        $invoice->delete();

        // Verify all are soft-deleted
        $this->assertSoftDeleted('invoices', ['id' => $invoice->id]);
        $this->assertSoftDeleted('invoice_items', ['id' => $item->id]);
        $this->assertSoftDeleted('invoice_payments', ['id' => $payment->id]);
        $this->assertSoftDeleted('receipts', ['id' => $receiptId]);
        $this->assertSoftDeleted('transactions', ['id' => $transactionId]);

        // Restore invoice
        $invoice->restore();

        // Verify all are restored
        $this->assertNotSoftDeleted('invoices', ['id' => $invoice->id]);
        $this->assertNotSoftDeleted('invoice_items', ['id' => $item->id]);
        $this->assertNotSoftDeleted('invoice_payments', ['id' => $payment->id]);
        $this->assertNotSoftDeleted('receipts', ['id' => $receiptId]);
        $this->assertNotSoftDeleted('transactions', ['id' => $transactionId]);
    }

    /**
     * Test that user can only see their own non-deleted invoices
     */
    public function test_user_only_sees_own_non_deleted_invoices(): void
    {
        $user1 = User::factory()->create(['email_verified_at' => now()]);
        $user2 = User::factory()->create(['email_verified_at' => now()]);

        // User 1 invoices
        $user1Invoice1 = Invoice::create([
            'user_id' => $user1->id,
            'invoice_number' => 'INV-USER1-1',
            'client_name' => 'Client 1',
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'status' => 'draft',
            'total_amount' => 100.00,
        ]);

        $user1Invoice2 = Invoice::create([
            'user_id' => $user1->id,
            'invoice_number' => 'INV-USER1-2',
            'client_name' => 'Client 2',
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'status' => 'draft',
            'total_amount' => 200.00,
        ]);

        // User 2 invoice
        $user2Invoice = Invoice::create([
            'user_id' => $user2->id,
            'invoice_number' => 'INV-USER2-1',
            'client_name' => 'Client 3',
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'status' => 'draft',
            'total_amount' => 300.00,
        ]);

        // Delete user 1's second invoice
        $user1Invoice2->delete();

        // User 1 should only see their first invoice
        $response = $this->actingAs($user1)->get(route('invoices.index'));
        $response->assertSee('INV-USER1-1');
        $response->assertDontSee('INV-USER1-2'); // Deleted
        $response->assertDontSee('INV-USER2-1'); // Other user's

        // User 2 should only see their invoice
        $response = $this->actingAs($user2)->get(route('invoices.index'));
        $response->assertSee('INV-USER2-1');
        $response->assertDontSee('INV-USER1-1'); // Other user's
        $response->assertDontSee('INV-USER1-2'); // Other user's and deleted
    }
}

