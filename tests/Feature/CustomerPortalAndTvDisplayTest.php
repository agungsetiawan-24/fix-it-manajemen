<?php

namespace Tests\Feature;

use App\Enums\TicketStatus;
use App\Models\Customer;
use App\Models\ServiceTicket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerPortalAndTvDisplayTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_portal_search_page_is_publicly_accessible(): void
    {
        $response = $this->get('/tracking');
        $response->assertStatus(200);
        $response->assertSee('Lacak Status Servis Smartphone');

        $aliasResponse = $this->get('/cek-servis');
        $aliasResponse->assertStatus(200);
    }

    public function test_searching_by_ticket_code_redirects_to_detail(): void
    {
        $ticket = ServiceTicket::factory()->create(['ticket_code' => 'SRV-TEST-9999']);

        $response = $this->get('/tracking?q=SRV-TEST-9999');
        $response->assertRedirect(route('tracking.detail', 'SRV-TEST-9999'));
    }

    public function test_searching_by_phone_number_returns_matching_tickets(): void
    {
        $customer = Customer::factory()->create(['phone' => '081299998888']);
        $ticket1 = ServiceTicket::factory()->create([
            'customer_id' => $customer->id,
            'device_model' => 'iPhone 15 Pro Max',
        ]);
        $ticket2 = ServiceTicket::factory()->create([
            'customer_id' => $customer->id,
            'device_model' => 'Galaxy S24 Ultra',
        ]);

        $response = $this->get('/tracking?q=081299998888');
        $response->assertStatus(200);
        $response->assertSee('iPhone 15 Pro Max');
        $response->assertSee('Galaxy S24 Ultra');
    }

    public function test_tracking_detail_renders_stepper_and_ticket_info(): void
    {
        $ticket = ServiceTicket::factory()->create([
            'ticket_code' => 'SRV-DETAIL-001',
            'status' => TicketStatus::Proses,
        ]);

        $response = $this->get("/track/{$ticket->ticket_code}");
        $response->assertStatus(200);
        $response->assertSee($ticket->ticket_code);
        $response->assertSee('Tahapan Pengerjaan Unit:');
        $response->assertSee($ticket->device_brand);
    }

    public function test_customer_can_approve_repair_cost_online(): void
    {
        $ticket = ServiceTicket::factory()->create([
            'ticket_code' => 'SRV-APPROVAL-001',
            'status' => TicketStatus::MenungguApproval,
            'total_cost' => 750000,
            'technician_notes' => 'Perlu ganti IC Display tambahan',
        ]);

        // Detail page should display the approval banner & buttons
        $detailResponse = $this->get("/track/{$ticket->ticket_code}");
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('Persetujuan Pelanggan Diperlukan');
        $detailResponse->assertSee('Setujui &amp; Lanjutkan Servis', false);

        // Submit approval
        $approveResponse = $this->post("/track/{$ticket->ticket_code}/approve");
        $approveResponse->assertRedirect();
        $approveResponse->assertSessionHas('success');

        $this->assertEquals(TicketStatus::Proses, $ticket->fresh()->status);
        $this->assertStringContainsString('Disetujui oleh pelanggan via portal online', $ticket->fresh()->technician_notes);
    }

    public function test_customer_can_reject_repair_cost_online(): void
    {
        $ticket = ServiceTicket::factory()->create([
            'ticket_code' => 'SRV-REJECT-001',
            'status' => TicketStatus::MenungguApproval,
        ]);

        $rejectResponse = $this->post("/track/{$ticket->ticket_code}/reject");
        $rejectResponse->assertRedirect();
        $rejectResponse->assertSessionHas('info');

        $this->assertEquals(TicketStatus::Batal, $ticket->fresh()->status);
        $this->assertStringContainsString('Dibatalkan oleh pelanggan via portal online', $ticket->fresh()->technician_notes);
    }

    public function test_tv_display_is_publicly_accessible(): void
    {
        $response = $this->get('/tv-display');
        $response->assertStatus(200);
        $response->assertSee('FIXIT SERVICE PRO');
        $response->assertSee('Sedang Dikerjakan');
        $response->assertSee('Siap Diambil');

        $aliasResponse = $this->get('/queue-board');
        $aliasResponse->assertStatus(200);
    }

    public function test_tv_display_api_returns_json_with_masked_names(): void
    {
        $customer = Customer::factory()->create(['name' => 'Budi Santoso']);
        ServiceTicket::factory()->create([
            'customer_id' => $customer->id,
            'status' => TicketStatus::SiapDiambil,
            'ticket_code' => 'SRV-TV-001',
        ]);

        $response = $this->getJson('/api/queue-board');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'in_progress',
            'ready_for_pickup',
            'timestamp',
            'counts',
        ]);

        $data = $response->json('ready_for_pickup');
        $this->assertNotEmpty($data);
        $this->assertEquals('SRV-TV-001', $data[0]['ticket_code']);
        // Verify privacy masking: "Budi Santoso" -> "Budi S***"
        $this->assertEquals('Budi S***', $data[0]['customer_name']);
        // Full name is available for TTS speech
        $this->assertEquals('Budi Santoso', $data[0]['full_customer_name']);
    }
}
