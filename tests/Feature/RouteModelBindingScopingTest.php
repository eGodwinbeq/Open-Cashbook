<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Chapter;
use App\Models\Invoice;
use App\Models\Receipt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RouteModelBindingScopingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that users can only access their own invoices
     */
    public function test_user_cannot_access_other_users_invoice(): void
    {
        // Create two users
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Create invoice for user 1
        $invoice = Invoice::create([
            'user_id' => $user1->id,
            'invoice_number' => 'INV-TEST-001',
            'client_name' => 'Test Client',
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'status' => 'draft',
            'total_amount' => 100.00,
        ]);

        // User 2 tries to access user 1's invoice
        $this->actingAs($user2)
             ->get(route('invoices.show', $invoice))
             ->assertStatus(404); // Should return 404, not 403

        // User 2 tries to edit user 1's invoice
        $this->actingAs($user2)
             ->get(route('invoices.edit', $invoice))
             ->assertStatus(404);

        // User 2 tries to access payment form for user 1's invoice
        $this->actingAs($user2)
             ->get(route('invoices.payment', $invoice))
             ->assertStatus(404);
    }

    /**
     * Test that users CAN access their own invoices
     */
    public function test_user_can_access_own_invoice(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $invoice = Invoice::create([
            'user_id' => $user->id,
            'invoice_number' => 'INV-TEST-002',
            'client_name' => 'Test Client',
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'status' => 'draft',
            'total_amount' => 100.00,
        ]);

        // User can access their own invoice
        $this->actingAs($user)
             ->get(route('invoices.show', $invoice))
             ->assertStatus(200);

        // User can access edit form
        $this->actingAs($user)
             ->get(route('invoices.edit', $invoice))
             ->assertStatus(200);

        // User can access payment form
        $this->actingAs($user)
             ->get(route('invoices.payment', $invoice))
             ->assertStatus(200);
    }

    /**
     * Test that users can only access their own chapters
     */
    public function test_user_cannot_access_other_users_chapter(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Create chapter for user 1
        $chapter = Chapter::create([
            'user_id' => $user1->id,
            'name' => 'Test Chapter',
            'description' => 'Test Description',
        ]);

        // User 2 tries to select user 1's chapter
        $this->actingAs($user2)
             ->post(route('chapters.select', $chapter))
             ->assertStatus(404);

        // User 2 tries to delete user 1's chapter
        $this->actingAs($user2)
             ->delete(route('chapters.destroy', $chapter))
             ->assertStatus(404);
    }

    /**
     * Test that users CAN access their own chapters
     */
    public function test_user_can_access_own_chapter(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $chapter = Chapter::create([
            'user_id' => $user->id,
            'name' => 'Test Chapter',
            'description' => 'Test Description',
        ]);

        // User can select their own chapter
        $this->actingAs($user)
             ->post(route('chapters.select', $chapter))
             ->assertRedirect(route('dashboard'));

        // User can delete their own chapter
        $chapter2 = Chapter::create([
            'user_id' => $user->id,
            'name' => 'Test Chapter 2',
        ]);

        $this->actingAs($user)
             ->delete(route('chapters.destroy', $chapter2))
             ->assertRedirect(route('dashboard'));

        $this->assertSoftDeleted('chapters', ['id' => $chapter2->id]);
    }

    /**
     * Test invoice actions work correctly for owner
     */
    public function test_invoice_actions_work_for_owner(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $chapter = Chapter::create([
            'user_id' => $user->id,
            'name' => 'Financial',
        ]);

        $invoice = Invoice::create([
            'user_id' => $user->id,
            'chapter_id' => $chapter->id,
            'invoice_number' => 'INV-TEST-003',
            'client_name' => 'Test Client',
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'status' => 'draft',
            'total_amount' => 100.00,
            'balance_due' => 100.00,
        ]);

        // Mark as paid
        $this->actingAs($user)
             ->post(route('invoices.mark-paid', $invoice))
             ->assertRedirect();

        $invoice->refresh();
        $this->assertEquals('paid', $invoice->status);

        // Download invoice
        $this->actingAs($user)
             ->get(route('invoices.download', $invoice))
             ->assertStatus(200);
    }

    /**
     * Test invoice actions fail for non-owner
     */
    public function test_invoice_actions_fail_for_non_owner(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $invoice = Invoice::create([
            'user_id' => $user1->id,
            'invoice_number' => 'INV-TEST-004',
            'client_name' => 'Test Client',
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'status' => 'draft',
            'total_amount' => 100.00,
            'balance_due' => 100.00,
        ]);

        // User 2 tries to mark user 1's invoice as paid
        $this->actingAs($user2)
             ->post(route('invoices.mark-paid', $invoice))
             ->assertStatus(404);

        // User 2 tries to download user 1's invoice
        $this->actingAs($user2)
             ->get(route('invoices.download', $invoice))
             ->assertStatus(404);

        // User 2 tries to delete user 1's invoice
        $this->actingAs($user2)
             ->delete(route('invoices.destroy', $invoice))
             ->assertStatus(404);
    }

    /**
     * Test revenue report only shows user's own invoices
     */
    public function test_revenue_report_shows_only_own_invoices(): void
    {
        $user1 = User::factory()->create(['email_verified_at' => now()]);
        $user2 = User::factory()->create(['email_verified_at' => now()]);

        // Create invoice for user 1
        Invoice::create([
            'user_id' => $user1->id,
            'invoice_number' => 'INV-USER1',
            'client_name' => 'User 1 Client',
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'status' => 'paid',
            'total_amount' => 500.00,
            'paid_amount' => 500.00,
            'balance_due' => 0,
        ]);

        // Create invoice for user 2
        Invoice::create([
            'user_id' => $user2->id,
            'invoice_number' => 'INV-USER2',
            'client_name' => 'User 2 Client',
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'status' => 'paid',
            'total_amount' => 1000.00,
            'paid_amount' => 1000.00,
            'balance_due' => 0,
        ]);

        // User 1 accesses revenue report
        $response = $this->actingAs($user1)
                         ->get(route('revenue.report'))
                         ->assertStatus(200);

        // Should see their own invoice but not user 2's
        $response->assertSee('INV-USER1');
        $response->assertDontSee('INV-USER2');
        $response->assertSee('500'); // Their amount
        $response->assertDontSee('1000'); // Other user's amount
    }
}

