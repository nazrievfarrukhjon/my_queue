<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use Illuminate\Http\Request;

class QueueController extends Controller
{
    public function getByTerminalId(Request $request) {
        return Queue::where('terminal_id', $request->terminalId)->get();
    }
}
