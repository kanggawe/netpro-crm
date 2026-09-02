<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Ticket::with('customer.package');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $tickets = $query->orderBy('id', 'desc')->paginate($request->get('per_page', 20));

        return response()->json([
            'status' => 'success',
            'data' => $tickets,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'category' => 'required|string|max:100',
            'priority' => 'nullable|in:LOW,MEDIUM,HIGH,CRITICAL',
            'assigned_tech' => 'nullable|string|max:100',
            'sla_minutes' => 'nullable|integer',
            'description' => 'nullable|string',
        ]);

        $ticketNo = 'TCK-' . Carbon::now()->format('Ymd') . '-' . rand(1000, 9999);
        $ticket = Ticket::create(array_merge($validated, [
            'ticket_no' => $ticketNo,
            'status' => 'OPEN',
        ]));

        return response()->json([
            'status' => 'success',
            'message' => "Tiket gangguan {$ticket->ticket_no} berhasil dibuka.",
            'data' => $ticket->load('customer'),
        ], 201);
    }

    public function resolve(int $id, Request $request): JsonResponse
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->update([
            'status' => 'RESOLVED',
            'solution' => $request->get('solution', 'Pemeriksaan kabel drop dan konfigurasi ONU selesai.'),
            'closed_at' => Carbon::now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => "Tiket {$ticket->ticket_no} telah ditandai terselesaikan.",
            'data' => $ticket,
        ]);
    }
}
