<?php

namespace Database\Factories;

use App\Enums\TicketStatus;
use App\Models\Customer;
use App\Models\ServiceTicket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServiceTicket>
 */
class ServiceTicketFactory extends Factory
{
    protected $model = ServiceTicket::class;

    public function definition(): array
    {
        return [
            'ticket_code' => 'SRV-'.now()->format('Ym').'-'.fake()->unique()->numerify('####'),
            'customer_id' => Customer::factory(),
            'technician_id' => User::factory()->technician(),
            'device_brand' => fake()->randomElement(['Apple', 'Samsung', 'Xiaomi', 'Oppo', 'Vivo', 'Realme']),
            'device_model' => fake()->randomElement(['iPhone 13', 'iPhone 14 Pro', 'Galaxy S22', 'Redmi Note 12', 'Find X5']),
            'device_imei' => fake()->numerify('86##############'),
            'device_color' => fake()->safeColorName(),
            'encrypted_device_pin' => (string) fake()->numberBetween(1000, 9999),
            'complaint_notes' => fake()->randomElement(['Layar retak dan sentuh tidak merespons', 'Baterai boros dan cepat panas', 'Mati total setelah terkena air', 'Sinyal hilang / tidak terbaca']),
            'technician_notes' => fake()->optional()->sentence(),
            'status' => fake()->randomElement(TicketStatus::cases()),
            'total_cost' => fake()->numberBetween(150000, 1500000),
            'warranty_days' => fake()->randomElement([0, 14, 30, 90]),
            'warranty_expiry_date' => now()->addDays(30),
        ];
    }
}
