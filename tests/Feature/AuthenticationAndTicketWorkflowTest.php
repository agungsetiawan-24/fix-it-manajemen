<?php

namespace Tests\Feature;

use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Models\ServiceTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationAndTicketWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_home_and_dashboard_to_login(): void
    {
        $responseHome = $this->get('/');
        $responseHome->assertRedirect(route('login'));

        $responseDashboard = $this->get('/dashboard');
        $responseDashboard->assertRedirect(route('login'));
    }

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('FixIt Pro');
        $response->assertSee('admin@fixit.test');
    }

    public function test_user_can_authenticate_and_redirect_to_dashboard(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@fixit.test',
            'role' => UserRole::Admin,
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@fixit.test',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('dashboard'));

        $homeResponse = $this->actingAs($user)->get('/');
        $homeResponse->assertRedirect(route('dashboard'));
    }

    public function test_dashboard_renders_with_metrics_for_authenticated_user(): void
    {
        $admin = User::factory()->admin()->create();
        $ticket = ServiceTicket::factory()->create(['status' => TicketStatus::Antrian]);

        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Dashboard Utama');
        $response->assertSee($ticket->ticket_code);
    }

    public function test_service_tickets_index_renders_and_filters(): void
    {
        $tech = User::factory()->technician()->create();
        $ticket1 = ServiceTicket::factory()->create(['status' => TicketStatus::Antrian]);
        $ticket2 = ServiceTicket::factory()->create(['status' => TicketStatus::SiapDiambil]);

        $response = $this->actingAs($tech)->get('/tickets');
        $response->assertStatus(200);
        $response->assertSee($ticket1->ticket_code);
        $response->assertSee($ticket2->ticket_code);

        // Filter by status
        $filterResponse = $this->actingAs($tech)->get('/tickets?status=siap_diambil');
        $filterResponse->assertStatus(200);
        $filterResponse->assertSee($ticket2->ticket_code);
    }

    public function test_ticket_intake_creates_customer_ticket_and_checklists(): void
    {
        $admin = User::factory()->admin()->create();
        $tech = User::factory()->technician()->create();

        $formData = [
            'customer_mode' => 'new',
            'customer_name' => 'Bambang Sudiro',
            'customer_phone' => '085678901234',
            'customer_email' => 'bambang@example.com',
            'customer_address' => 'Jl. Pahlawan No. 9',
            'device_brand' => 'Apple',
            'device_model' => 'iPhone 14 Pro',
            'device_color' => 'Space Black',
            'device_imei' => '861234567890123',
            'encrypted_device_pin' => '2580',
            'complaint_notes' => 'Layar retak total setelah terjatuh dari motor',
            'technician_id' => $tech->id,
            'estimated_cost' => 1250000,
            'warranty_days' => 30,
            'checklists' => [
                [
                    'item_name' => 'Layar / LCD & Touchscreen',
                    'condition_before' => 'rusak',
                    'notes' => 'Kaca pecah, LCD blank',
                ],
                [
                    'item_name' => 'Kamera Depan',
                    'condition_before' => 'normal',
                    'notes' => null,
                ],
                [
                    'item_name' => 'Fisik Casing & Backdoor',
                    'condition_before' => 'baret',
                    'notes' => 'Dent di pojok bawah',
                ],
            ],
        ];

        $response = $this->actingAs($admin)->post('/tickets', $formData);

        $this->assertDatabaseHas('customers', [
            'name' => 'Bambang Sudiro',
            'phone' => '085678901234',
        ]);

        $ticket = ServiceTicket::where('device_model', 'iPhone 14 Pro')->first();
        $this->assertNotNull($ticket);
        $this->assertEquals(TicketStatus::Antrian, $ticket->status);
        $this->assertEquals('2580', $ticket->encrypted_device_pin);
        $this->assertCount(3, $ticket->checklists);

        $response->assertRedirect(route('tickets.show', $ticket->id));
    }

    public function test_ticket_status_can_be_updated(): void
    {
        $tech = User::factory()->technician()->create();
        $ticket = ServiceTicket::factory()->create(['status' => TicketStatus::Antrian]);

        $response = $this->actingAs($tech)->patch("/tickets/{$ticket->id}/status", [
            'status' => 'proses',
            'technician_id' => $tech->id,
            'technician_notes' => 'Pembersihan lem LCD lama selesai',
            'total_cost' => 500000,
            'warranty_days' => 14,
        ]);

        $response->assertRedirect();
        $this->assertEquals(TicketStatus::Proses, $ticket->fresh()->status);
        $this->assertEquals($tech->id, $ticket->fresh()->technician_id);
    }

    public function test_thermal_receipt_renders_correctly(): void
    {
        $tech = User::factory()->technician()->create();
        $ticket = ServiceTicket::factory()->create();

        $response = $this->actingAs($tech)->get("/tickets/{$ticket->id}/receipt");
        $response->assertStatus(200);
        $response->assertSee('FIX-IT SERVICE PRO');
        $response->assertSee($ticket->ticket_code);
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');
        $this->assertGuest();
        $response->assertRedirect(route('login'));
    }
}
