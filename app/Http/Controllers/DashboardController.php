<?php

namespace App\Http\Controllers;

use App\Enums\TicketStatus;
use App\Models\Customer;
use App\Models\FinancialTransaction;
use App\Models\InventorySparepart;
use App\Models\ServiceTicket;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index(): View
    {
        // Ticket Metrics
        $totalActiveTickets = ServiceTicket::active()->count();
        $queueCount = ServiceTicket::status(TicketStatus::Antrian)->count();
        $inProgressCount = ServiceTicket::whereIn('status', [
            TicketStatus::Diagnosis->value,
            TicketStatus::Proses->value,
            TicketStatus::TestingQc->value,
        ])->count();
        $waitingApprovalCount = ServiceTicket::status(TicketStatus::MenungguApproval)->count();
        $readyForPickupCount = ServiceTicket::status(TicketStatus::SiapDiambil)->count();
        $completedTodayCount = ServiceTicket::status(TicketStatus::Selesai)
            ->whereDate('updated_at', today())
            ->count();

        // Financial & Inventory Metrics
        $incomeToday = FinancialTransaction::income()
            ->whereDate('created_at', today())
            ->sum('amount');

        $lowStockCount = InventorySparepart::lowStock()->count();
        $totalCustomers = Customer::count();
        $totalTechnicians = User::technicians()->count();

        // Recent Tickets needing attention
        $urgentTickets = ServiceTicket::with(['customer', 'technician'])
            ->whereIn('status', [
                TicketStatus::Antrian->value,
                TicketStatus::Diagnosis->value,
                TicketStatus::MenungguApproval->value,
            ])
            ->latest()
            ->take(5)
            ->get();

        // Recent Completed or In Progress Tickets
        $recentTickets = ServiceTicket::with(['customer', 'technician'])
            ->latest()
            ->take(8)
            ->get();

        return view('dashboard', compact(
            'totalActiveTickets',
            'queueCount',
            'inProgressCount',
            'waitingApprovalCount',
            'readyForPickupCount',
            'completedTodayCount',
            'incomeToday',
            'lowStockCount',
            'totalCustomers',
            'totalTechnicians',
            'urgentTickets',
            'recentTickets'
        ));
    }
}
