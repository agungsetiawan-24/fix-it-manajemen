<?php

namespace Tests\Feature;

use App\Enums\ChecklistCondition;
use App\Enums\TicketStatus;
use App\Enums\TransactionType;
use App\Models\Customer;
use App\Models\FinancialTransaction;
use App\Models\InventorySparepart;
use App\Models\ServiceTicket;
use App\Models\TicketChecklist;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class DatabaseSchemaAndModelsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_model_has_uuid_role_and_relations(): void
    {
        $admin = User::factory()->admin()->create();
        $technician = User::factory()->technician()->create();

        $this->assertTrue(Str::isUuid($admin->id));
        $this->assertTrue(Str::isUuid($technician->id));
        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($admin->isTeknisi());
        $this->assertTrue($technician->isTeknisi());

        $this->assertEquals(1, User::admins()->count());
        $this->assertEquals(1, User::technicians()->count());
    }

    public function test_customer_model_has_uuid_and_relations(): void
    {
        $customer = Customer::factory()->create([
            'name' => 'Budi Santoso',
            'phone' => '08123456789',
        ]);

        $ticket = ServiceTicket::factory()->create([
            'customer_id' => $customer->id,
        ]);

        $this->assertTrue(Str::isUuid($customer->id));
        $this->assertCount(1, $customer->serviceTickets);
        $this->assertEquals($ticket->id, $customer->latestTicket->id);
    }

    public function test_inventory_sparepart_model_and_scopes(): void
    {
        $sparepart = InventorySparepart::factory()->create([
            'stock' => 2,
            'min_stock' => 5,
            'category' => 'LCD',
            'buy_price' => 500000,
            'sell_price' => 750000,
        ]);

        $this->assertTrue(Str::isUuid($sparepart->id));
        $this->assertTrue($sparepart->isLowStock());
        $this->assertEquals(1, InventorySparepart::lowStock()->count());
        $this->assertEquals(1, InventorySparepart::category('LCD')->count());
    }

    public function test_service_ticket_relationships_and_encrypted_pin(): void
    {
        $customer = Customer::factory()->create();
        $technician = User::factory()->technician()->create();

        $ticket = ServiceTicket::create([
            'ticket_code' => 'SRV-TEST-001',
            'customer_id' => $customer->id,
            'technician_id' => $technician->id,
            'device_brand' => 'Apple',
            'device_model' => 'iPhone 13',
            'encrypted_device_pin' => '987654',
            'complaint_notes' => 'Layar bergaris',
            'status' => TicketStatus::SiapDiambil,
            'total_cost' => 850000.50,
            'warranty_days' => 30,
            'warranty_expiry_date' => now()->addDays(30)->toDateString(),
        ]);

        $this->assertTrue(Str::isUuid($ticket->id));
        $this->assertEquals('987654', $ticket->encrypted_device_pin);
        $this->assertEquals(TicketStatus::SiapDiambil, $ticket->status);
        $this->assertTrue($ticket->isWarrantyActive());
        $this->assertEquals($customer->id, $ticket->customer->id);
        $this->assertEquals($technician->id, $ticket->technician->id);

        // Checklist relation
        $checklist = TicketChecklist::create([
            'ticket_id' => $ticket->id,
            'item_name' => 'Layar / LCD',
            'condition_before' => ChecklistCondition::Rusak,
            'condition_after' => ChecklistCondition::Normal,
        ]);

        $this->assertTrue(Str::isUuid($checklist->id));
        $this->assertEquals($ticket->id, $checklist->ticket->id);
        $this->assertCount(1, $ticket->checklists);

        // Spareparts Many-to-Many relation
        $sparepart = InventorySparepart::factory()->create();
        $ticket->spareparts()->attach($sparepart->id, [
            'quantity' => 1,
            'buy_price' => 500000,
            'sell_price' => 750000,
            'status' => 'approved',
        ]);

        $this->assertCount(1, $ticket->fresh()->spareparts);
        $this->assertEquals('approved', $ticket->fresh()->spareparts->first()->pivot->status);

        // Financial transaction relation
        $transaction = FinancialTransaction::create([
            'ticket_id' => $ticket->id,
            'user_id' => $technician->id,
            'type' => TransactionType::PemasukanServis,
            'amount' => 850000.50,
            'modal_amount' => 500000,
            'net_profit' => 350000.50,
            'payment_method' => 'qris',
        ]);

        $this->assertTrue(Str::isUuid($transaction->id));
        $this->assertEquals($ticket->id, $transaction->ticket->id);
        $this->assertEquals($technician->id, $transaction->user->id);
        $this->assertCount(1, $ticket->fresh()->financialTransactions);
        $this->assertEquals(1, FinancialTransaction::income()->count());
    }

    public function test_database_seeder_executes_successfully(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseHas('users', ['email' => 'admin@fixit.test']);
        $this->assertDatabaseHas('users', ['email' => 'teknisi1@fixit.test']);
        $this->assertDatabaseHas('customers', ['phone' => '081234567890']);
        $this->assertDatabaseHas('service_tickets', ['ticket_code' => 'SRV-202610-001']);
        $this->assertDatabaseHas('service_tickets', ['ticket_code' => 'SRV-202610-002']);
        $this->assertDatabaseHas('ticket_checklists', ['item_name' => 'Layar / LCD']);
        $this->assertDatabaseHas('inventory_spareparts', ['part_code' => 'LCD-IP13-OEM']);
        $this->assertDatabaseHas('financial_transactions', ['type' => TransactionType::PemasukanServis->value]);
    }
}
