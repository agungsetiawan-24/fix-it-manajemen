<?php

namespace Database\Factories;

use App\Enums\ChecklistCondition;
use App\Models\ServiceTicket;
use App\Models\TicketChecklist;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TicketChecklist>
 */
class TicketChecklistFactory extends Factory
{
    protected $model = TicketChecklist::class;

    public function definition(): array
    {
        return [
            'ticket_id' => ServiceTicket::factory(),
            'item_name' => fake()->randomElement(['Layar / LCD', 'Kamera Depan', 'Kamera Belakang', 'Face ID / Touch ID', 'Speaker Atas', 'Speaker Bawah', 'Mikrofon', 'Port Charger', 'Tombol Power / Volume', 'Baterai / Kondisi Fisik']),
            'condition_before' => fake()->randomElement(ChecklistCondition::cases()),
            'condition_after' => fake()->optional()->randomElement(ChecklistCondition::cases()),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
