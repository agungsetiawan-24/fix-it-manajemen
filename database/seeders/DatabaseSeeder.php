<?php

namespace Database\Seeders;

use App\Enums\ChecklistCondition;
use App\Enums\TicketStatus;
use App\Enums\TransactionType;
use App\Enums\UserRole;
use App\Models\Customer;
use App\Models\FinancialTransaction;
use App\Models\InventorySparepart;
use App\Models\ServiceTicket;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Default Users (Admin & Teknisi)
        $admin = User::firstOrCreate(
            ['email' => 'admin@fixit.test'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('password'),
                'role' => UserRole::Admin,
                'email_verified_at' => now(),
            ]
        );

        $teknisi1 = User::firstOrCreate(
            ['email' => 'teknisi1@fixit.test'],
            [
                'name' => 'Budi Teknisi Senior',
                'password' => Hash::make('password'),
                'role' => UserRole::Teknisi,
                'email_verified_at' => now(),
            ]
        );

        $teknisi2 = User::firstOrCreate(
            ['email' => 'teknisi2@fixit.test'],
            [
                'name' => 'Agus Teknisi Hardware',
                'password' => Hash::make('password'),
                'role' => UserRole::Teknisi,
                'email_verified_at' => now(),
            ]
        );

        // 2. Create Customers
        $customer1 = Customer::firstOrCreate(
            ['phone' => '081234567890'],
            [
                'name' => 'Andi Pratama',
                'email' => 'andi@example.com',
                'address' => 'Jl. Sudirman No. 12, Jakarta Pusat',
            ]
        );

        $customer2 = Customer::firstOrCreate(
            ['phone' => '089876543210'],
            [
                'name' => 'Siti Nurhaliza',
                'email' => 'siti@example.com',
                'address' => 'Jl. Melati No. 45, Bandung',
            ]
        );

        // 3. Create Inventory Spareparts
        $lcdIphone = InventorySparepart::firstOrCreate(
            ['part_code' => 'LCD-IP13-OEM'],
            [
                'part_name' => 'LCD Display iPhone 13 Original OEM',
                'category' => 'LCD',
                'stock' => 15,
                'min_stock' => 3,
                'buy_price' => 650000,
                'sell_price' => 950000,
                'description' => 'Original OEM Screen Assembly Retina Display',
            ]
        );

        $batteryIphone = InventorySparepart::firstOrCreate(
            ['part_code' => 'BAT-IP13-ORI'],
            [
                'part_name' => 'Baterai iPhone 13 High Capacity 3227mAh',
                'category' => 'Baterai',
                'stock' => 20,
                'min_stock' => 5,
                'buy_price' => 250000,
                'sell_price' => 450000,
                'description' => 'Baterai original capacity dengan chip garansi',
            ]
        );

        $portCharger = InventorySparepart::firstOrCreate(
            ['part_code' => 'PRT-SS22-FLX'],
            [
                'part_name' => 'Fleksibel Port Charger Samsung S22',
                'category' => 'Fleksibel',
                'stock' => 8,
                'min_stock' => 2,
                'buy_price' => 95000,
                'sell_price' => 200000,
                'description' => 'Board charger include microphone dan antenna',
            ]
        );

        // 4. Create Service Ticket 1
        $ticket1 = ServiceTicket::firstOrCreate(
            ['ticket_code' => 'SRV-202610-001'],
            [
                'customer_id' => $customer1->id,
                'technician_id' => $teknisi1->id,
                'device_brand' => 'Apple',
                'device_model' => 'iPhone 13 Pro',
                'device_imei' => '861234567890123',
                'device_color' => 'Sierra Blue',
                'encrypted_device_pin' => '123456',
                'complaint_notes' => 'Layar bergaris hijau dan tidak bisa disentuh setelah terjatuh',
                'technician_notes' => 'Ganti LCD selesai, pengerjaan QC berhasil',
                'status' => TicketStatus::SiapDiambil,
                'total_cost' => 1150000,
                'warranty_days' => 30,
                'warranty_expiry_date' => now()->addDays(30),
            ]
        );

        // Checklist for Ticket 1
        $ticket1->checklists()->createMany([
            [
                'item_name' => 'Layar / LCD',
                'condition_before' => ChecklistCondition::Rusak,
                'condition_after' => ChecklistCondition::Normal,
                'notes' => 'Sudah diganti dengan LCD OEM baru',
            ],
            [
                'item_name' => 'Face ID',
                'condition_before' => ChecklistCondition::Normal,
                'condition_after' => ChecklistCondition::Normal,
                'notes' => 'Face ID berfungsi normal setelah translasi sensor',
            ],
            [
                'item_name' => 'Kamera Belakang',
                'condition_before' => ChecklistCondition::Normal,
                'condition_after' => ChecklistCondition::Normal,
                'notes' => 'Fungsi fokus dan zoom normal',
            ],
            [
                'item_name' => 'Fisik Casing',
                'condition_before' => ChecklistCondition::Baret,
                'condition_after' => ChecklistCondition::Baret,
                'notes' => 'Baret halus di sudut kanan atas bawaan unit',
            ],
        ]);

        // Attach Sparepart to Ticket 1
        $ticket1->spareparts()->syncWithoutDetaching([
            $lcdIphone->id => [
                'quantity' => 1,
                'buy_price' => $lcdIphone->buy_price,
                'sell_price' => $lcdIphone->sell_price,
                'status' => 'approved',
            ],
        ]);

        // 5. Create Financial Transaction for Ticket 1
        FinancialTransaction::create([
            'ticket_id' => $ticket1->id,
            'user_id' => $admin->id,
            'type' => TransactionType::PemasukanServis,
            'amount' => 1150000,
            'modal_amount' => 650000,
            'net_profit' => 500000,
            'payment_method' => 'qris',
            'notes' => 'Pembayaran lunas tiket SRV-202610-001 via QRIS',
        ]);

        // 6. Create Service Ticket 2
        $ticket2 = ServiceTicket::firstOrCreate(
            ['ticket_code' => 'SRV-202610-002'],
            [
                'customer_id' => $customer2->id,
                'technician_id' => $teknisi2->id,
                'device_brand' => 'Samsung',
                'device_model' => 'Galaxy S22',
                'device_imei' => '359876543210987',
                'device_color' => 'Phantom Black',
                'encrypted_device_pin' => '9988',
                'complaint_notes' => 'Tidak bisa mengisi daya, port goyang dan panas',
                'technician_notes' => 'Menunggu persetujuan customer untuk penggantian fleksibel charger',
                'status' => TicketStatus::MenungguApproval,
                'total_cost' => 300000,
                'warranty_days' => 14,
                'warranty_expiry_date' => null,
            ]
        );

        $ticket2->checklists()->createMany([
            [
                'item_name' => 'Port Charger',
                'condition_before' => ChecklistCondition::Rusak,
                'condition_after' => null,
                'notes' => 'Pin charger patah dan berkarat',
            ],
            [
                'item_name' => 'Layar / LCD',
                'condition_before' => ChecklistCondition::Normal,
                'condition_after' => null,
                'notes' => 'Normal tidak ada kendala',
            ],
        ]);
    }
}
