<?php

namespace App\Http\Controllers;

use App\Enums\TicketStatus;
use App\Models\ServiceTicket;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class TvDisplayController extends Controller
{
    /**
     * Show the full-screen TV queue board.
     */
    public function index(): View
    {
        $inProgress = ServiceTicket::with(['customer', 'technician'])
            ->whereIn('status', [
                TicketStatus::Diagnosis->value,
                TicketStatus::Proses->value,
                TicketStatus::TestingQc->value,
            ])
            ->latest('updated_at')
            ->take(8)
            ->get();

        $readyForPickup = ServiceTicket::with(['customer', 'technician'])
            ->status(TicketStatus::SiapDiambil)
            ->latest('updated_at')
            ->take(8)
            ->get();

        return view('tv.display', compact('inProgress', 'readyForPickup'));
    }

    /**
     * API endpoint for live polling queue status and triggering TTS.
     */
    public function queueData(): JsonResponse
    {
        $inProgress = ServiceTicket::with(['customer', 'technician'])
            ->whereIn('status', [
                TicketStatus::Diagnosis->value,
                TicketStatus::Proses->value,
                TicketStatus::TestingQc->value,
            ])
            ->latest('updated_at')
            ->take(10)
            ->get()
            ->map(function ($ticket) {
                return [
                    'id' => $ticket->id,
                    'ticket_code' => $ticket->ticket_code,
                    'customer_name' => $this->maskName($ticket->customer->name),
                    'device' => "{$ticket->device_brand} {$ticket->device_model}",
                    'status' => $ticket->status->value,
                    'status_label' => $ticket->status->label(),
                    'status_color' => $ticket->status->color(),
                    'technician' => $ticket->technician?->name ?? 'Teknisi',
                ];
            });

        $readyForPickup = ServiceTicket::with(['customer', 'technician'])
            ->status(TicketStatus::SiapDiambil)
            ->latest('updated_at')
            ->take(8)
            ->get()
            ->map(function ($ticket) {
                return [
                    'id' => $ticket->id,
                    'ticket_code' => $ticket->ticket_code,
                    'customer_name' => $this->maskName($ticket->customer->name),
                    'full_customer_name' => $ticket->customer->name,
                    'device' => "{$ticket->device_brand} {$ticket->device_model}",
                    'updated_at' => $ticket->updated_at->toIso8601String(),
                ];
            });

        return response()->json([
            'in_progress' => $inProgress,
            'ready_for_pickup' => $readyForPickup,
            'timestamp' => now()->toIso8601String(),
            'counts' => [
                'antrian' => ServiceTicket::status(TicketStatus::Antrian)->count(),
                'in_progress' => $inProgress->count(),
                'ready_for_pickup' => $readyForPickup->count(),
                'completed_today' => ServiceTicket::status(TicketStatus::Selesai)->whereDate('updated_at', today())->count(),
            ],
        ]);
    }

    /**
     * Mask customer name for TV privacy (e.g., "Andi Pratama" -> "Andi P***").
     */
    private function maskName(string $name): string
    {
        $parts = explode(' ', trim($name));
        if (count($parts) === 1) {
            return $parts[0];
        }

        $firstName = $parts[0];
        $rest = '';
        for ($i = 1; $i < count($parts); $i++) {
            $initial = mb_substr($parts[$i], 0, 1);
            $rest .= " {$initial}***";
        }

        return $firstName.$rest;
    }
}
