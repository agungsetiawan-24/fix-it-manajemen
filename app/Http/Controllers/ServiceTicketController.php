<?php

namespace App\Http\Controllers;

use App\Enums\TicketStatus;
use App\Models\Customer;
use App\Models\InventorySparepart;
use App\Models\ServiceTicket;
use App\Models\TicketChecklist;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ServiceTicketController extends Controller
{
    /**
     * Display a listing of service tickets.
     */
    public function index(Request $request): View
    {
        $statusFilter = $request->input('status');
        $search = $request->input('search');
        $technicianFilter = $request->input('technician_id');

        $query = ServiceTicket::with(['customer', 'technician', 'checklists'])
            ->latest();

        if ($statusFilter && $statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        if ($technicianFilter) {
            $query->where('technician_id', $technicianFilter);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('ticket_code', 'like', "%{$search}%")
                    ->orWhere('device_brand', 'like', "%{$search}%")
                    ->orWhere('device_model', 'like', "%{$search}%")
                    ->orWhere('device_imei', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        $tickets = $query->paginate(12)->withQueryString();

        // Status counts for badge tabs
        $statusCounts = [
            'all' => ServiceTicket::count(),
            'antrian' => ServiceTicket::status(TicketStatus::Antrian)->count(),
            'diagnosis' => ServiceTicket::status(TicketStatus::Diagnosis)->count(),
            'menunggu_approval' => ServiceTicket::status(TicketStatus::MenungguApproval)->count(),
            'proses' => ServiceTicket::status(TicketStatus::Proses)->count(),
            'testing_qc' => ServiceTicket::status(TicketStatus::TestingQc)->count(),
            'siap_diambil' => ServiceTicket::status(TicketStatus::SiapDiambil)->count(),
            'selesai' => ServiceTicket::status(TicketStatus::Selesai)->count(),
            'batal' => ServiceTicket::status(TicketStatus::Batal)->count(),
        ];

        $technicians = User::technicians()->get();

        return view('tickets.index', compact(
            'tickets',
            'statusCounts',
            'statusFilter',
            'search',
            'technicianFilter',
            'technicians'
        ));
    }

    /**
     * Show the form for creating a new service ticket.
     */
    public function create(): View
    {
        $technicians = User::technicians()->get();
        $customers = Customer::latest()->take(20)->get();

        $defaultChecklistItems = [
            'Layar / LCD & Touchscreen',
            'Kamera Depan',
            'Kamera Belakang',
            'Face ID / Fingerprint',
            'Speaker Earpiece & Buzzer',
            'Mikrofon',
            'Port Charger & Pengisian',
            'Tombol Power & Volume',
            'Konektivitas WiFi & Sinyal',
            'Fisik Casing & Backdoor',
        ];

        return view('tickets.create', compact('technicians', 'customers', 'defaultChecklistItems'));
    }

    /**
     * Store a newly created service ticket in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            // Customer Validation
            'customer_mode' => ['required', 'string', 'in:new,existing'],
            'customer_id' => ['nullable', 'required_if:customer_mode,existing', 'exists:customers,id'],
            'customer_name' => ['nullable', 'required_if:customer_mode,new', 'string', 'max:255'],
            'customer_phone' => ['nullable', 'required_if:customer_mode,new', 'string', 'max:30'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'customer_address' => ['nullable', 'string', 'max:500'],

            // Device Info
            'device_brand' => ['required', 'string', 'max:100'],
            'device_model' => ['required', 'string', 'max:150'],
            'device_imei' => ['nullable', 'string', 'max:50'],
            'device_color' => ['nullable', 'string', 'max:50'],
            'encrypted_device_pin' => ['nullable', 'string', 'max:50'],
            'complaint_notes' => ['required', 'string'],
            'technician_id' => ['nullable', 'exists:users,id'],
            'estimated_cost' => ['nullable', 'numeric', 'min:0'],
            'warranty_days' => ['nullable', 'integer', 'min:0'],

            // Checklists
            'checklists' => ['required', 'array', 'min:1'],
            'checklists.*.item_name' => ['required', 'string', 'max:255'],
            'checklists.*.condition_before' => ['required', 'string', Rule::in(['normal', 'rusak', 'baret', 'mati'])],
            'checklists.*.notes' => ['nullable', 'string', 'max:255'],
        ]);

        $ticket = DB::transaction(function () use ($validated) {
            // 1. Resolve Customer
            if ($validated['customer_mode'] === 'existing') {
                $customerId = $validated['customer_id'];
            } else {
                $customer = Customer::create([
                    'name' => $validated['customer_name'],
                    'phone' => $validated['customer_phone'],
                    'email' => $validated['customer_email'] ?? null,
                    'address' => $validated['customer_address'] ?? null,
                ]);
                $customerId = $customer->id;
            }

            // 2. Generate Unique Ticket Code (Format: SRV-YYYYMM-XXXX)
            $prefix = 'SRV-'.now()->format('Ym').'-';
            $latestTicket = ServiceTicket::where('ticket_code', 'like', "{$prefix}%")
                ->orderByDesc('ticket_code')
                ->lockForUpdate()
                ->first();

            $nextSequence = 1;
            if ($latestTicket) {
                $lastNumber = (int) substr($latestTicket->ticket_code, -4);
                $nextSequence = $lastNumber + 1;
            }
            $ticketCode = $prefix.sprintf('%04d', $nextSequence);

            // 3. Create Service Ticket
            $ticket = ServiceTicket::create([
                'ticket_code' => $ticketCode,
                'customer_id' => $customerId,
                'technician_id' => $validated['technician_id'] ?? null,
                'device_brand' => $validated['device_brand'],
                'device_model' => $validated['device_model'],
                'device_imei' => $validated['device_imei'] ?? null,
                'device_color' => $validated['device_color'] ?? null,
                'encrypted_device_pin' => $validated['encrypted_device_pin'] ?? null,
                'complaint_notes' => $validated['complaint_notes'],
                'status' => TicketStatus::Antrian,
                'total_cost' => $validated['estimated_cost'] ?? 0,
                'warranty_days' => $validated['warranty_days'] ?? 0,
            ]);

            // 4. Create Intake Checklists
            foreach ($validated['checklists'] as $item) {
                TicketChecklist::create([
                    'ticket_id' => $ticket->id,
                    'item_name' => $item['item_name'],
                    'condition_before' => $item['condition_before'],
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            return $ticket;
        });

        return redirect()->route('tickets.show', $ticket->id)
            ->with('success', "Tiket servis [{$ticket->ticket_code}] berhasil didaftarkan ke antrian!");
    }

    /**
     * Display the specified service ticket.
     */
    public function show(ServiceTicket $ticket): View
    {
        $ticket->load(['customer', 'technician', 'checklists', 'spareparts', 'financialTransactions']);
        $technicians = User::technicians()->get();
        $spareparts = InventorySparepart::where('stock', '>', 0)->get();

        return view('tickets.show', compact('ticket', 'technicians', 'spareparts'));
    }

    /**
     * Update status and notes for the service ticket.
     */
    public function updateStatus(Request $request, ServiceTicket $ticket): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(array_column(TicketStatus::cases(), 'value'))],
            'technician_id' => ['nullable', 'exists:users,id'],
            'technician_notes' => ['nullable', 'string'],
            'total_cost' => ['nullable', 'numeric', 'min:0'],
            'warranty_days' => ['nullable', 'integer', 'min:0'],
        ]);

        $newStatus = TicketStatus::from($validated['status']);
        $ticket->status = $newStatus;

        if (array_key_exists('technician_id', $validated)) {
            $ticket->technician_id = $validated['technician_id'];
        }

        if (isset($validated['technician_notes'])) {
            $ticket->technician_notes = $validated['technician_notes'];
        }

        if (isset($validated['total_cost'])) {
            $ticket->total_cost = $validated['total_cost'];
        }

        if (isset($validated['warranty_days'])) {
            $ticket->warranty_days = $validated['warranty_days'];
        }

        // Set warranty expiry if finished
        if ($newStatus === TicketStatus::Selesai && $ticket->warranty_days > 0) {
            $ticket->warranty_expiry_date = now()->addDays($ticket->warranty_days);
        }

        $ticket->save();

        return back()->with('success', "Status tiket {$ticket->ticket_code} berhasil diperbarui menjadi {$newStatus->label()}.");
    }

    /**
     * Printable thermal receipt for the customer.
     */
    public function printReceipt(ServiceTicket $ticket): View
    {
        $ticket->load(['customer', 'technician', 'checklists']);

        return view('tickets.receipt', compact('ticket'));
    }
}
