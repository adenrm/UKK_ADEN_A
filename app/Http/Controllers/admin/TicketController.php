<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    //
    public function index()
    {
        $tickets = Ticket::all();
        return view('admin.tickets.index', compact('tickets'));
    }

    public function updateStatus(Request $request, $id)
    {
        $ticket = Ticket::find($id);
        $ticket->status = $request->status;
        $ticket->save();

        
        return redirect()->back()->with('success', 'Status berhasil diupdate');
    }
}
