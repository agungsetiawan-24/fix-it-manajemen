<?php

namespace App\Http\Controllers;

use App\Enums\TicketStatus;
use App\Models\Customer;
use App\Models\ServiceTicket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerPortalController extends Controller
{
    /**
     * Show tracking search page or search results.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $query = trim($request->input('q', ''));
        $tickets = collect();
        $searched = false;

        if ($query) {
            $searched = true;

            // If query directly matches ticket code format or starts with SRV-
            $exactTicket = ServiceTicket::where('ticket_code', $query)->first();
            if ($exactTicket) {
                return redirect()->route('tracking.detail', $exactTicket->ticket_code);
            }

            // Search by ticket code, customer phone, or customer name
            $cleanPhone = preg_replace('/[^0-9]/', '', $query);

            $tickets = ServiceTicket::with(['customer', 'technician'])
                ->where(function ($q) use ($query, $cleanPhone) {
                    $q->where('ticket_code', 'like', "%{$query}%");

                    if ($cleanPhone) {
                        $q->orWhereHas('customer', function ($cq) use ($cleanPhone) {
                            $cq->where('phone', 'like', "%{$cleanPhone}%");
                        });
                    }

                    $q->orWhereHas('customer', function ($cq) use ($query) {
                        $cq->where('name', 'like', "%{$query}%");
                    });
                })
                ->latest()
                ->take(10)
                ->get();

            // If only one exact match found by query, redirect directly to detail
            if ($tickets->count() === 1) {
                return redirect()->route('tracking.detail', $tickets->first()->ticket_code);
            }
        }

        return view('portal.tracking', compact('tickets', 'query', 'searched'));
    }

    /**
     * Show public tracking detail for a specific ticket code.
     */
    public function show(string $ticketCode): View
    {
        $ticket = ServiceTicket::with(['customer', 'technician', 'checklists', 'spareparts'])
            ->where('ticket_code', $ticketCode)
            ->firstOrFail();

        return view('portal.show', compact('ticket'));
    }

    /**
     * Customer approves additional repair cost online.
     */
    public function approve(Request $request, string $ticketCode): RedirectResponse
    {
        $ticket = ServiceTicket::where('ticket_code', $ticketCode)->firstOrFail();

        if ($ticket->status !== TicketStatus::MenungguApproval) {
            return back()->with('info', 'Status tiket ini tidak memerlukan persetujuan ulang.');
        }

        $ticket->status = TicketStatus::Proses;
        $note = now()->format('d/m/Y H:i').' - Disetujui oleh pelanggan via portal online.';
        $ticket->technician_notes = trim(($ticket->technician_notes ?? '')."\n".$note);
        $ticket->save();

        return back()->with('success', 'Terima kasih! Anda telah menyetujui estimasi perbaikan. Teknisi kami akan segera memproses unit HP Anda.');
    }

    /**
     * Customer rejects additional repair cost online.
     */
    public function reject(Request $request, string $ticketCode): RedirectResponse
    {
        $ticket = ServiceTicket::where('ticket_code', $ticketCode)->firstOrFail();

        if ($ticket->status !== TicketStatus::MenungguApproval) {
            return back()->with('info', 'Status tiket ini tidak memerlukan persetujuan ulang.');
        }

        $ticket->status = TicketStatus::Batal;
        $note = now()->format('d/m/Y H:i').' - Dibatalkan oleh pelanggan via portal online.';
        $ticket->technician_notes = trim(($ticket->technician_notes ?? '')."\n".$note);
        $ticket->save();

        return back()->with('info', 'Pengerjaan servis telah dibatalkan atas permintaan Anda. Silakan kunjungi toko kami untuk mengambil unit.');
    }
}
