<?php

namespace Database\Factories;

use App\Enums\TransactionType;
use App\Models\FinancialTransaction;
use App\Models\ServiceTicket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FinancialTransaction>
 */
class FinancialTransactionFactory extends Factory
{
    protected $model = FinancialTransaction::class;

    public function definition(): array
    {
        $type = fake()->randomElement(TransactionType::cases());
        $amount = fake()->numberBetween(100000, 1500000);
        $modalAmount = $type->isIncome() ? fake()->numberBetween(50000, $amount) : $amount;
        $netProfit = $type->isIncome() ? ($amount - $modalAmount) : (-1 * $amount);

        return [
            'ticket_id' => $type === TransactionType::PemasukanServis ? ServiceTicket::factory() : null,
            'user_id' => User::factory()->admin(),
            'type' => $type,
            'amount' => $amount,
            'modal_amount' => $modalAmount,
            'net_profit' => $netProfit,
            'payment_method' => fake()->randomElement(['tunai', 'transfer', 'qris']),
            'notes' => fake()->sentence(),
        ];
    }
}
