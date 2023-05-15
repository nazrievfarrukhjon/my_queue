<?php

namespace App\Http\Controllers;

use App\Models\Terminal;
use Illuminate\Http\Request;

class TerminalController extends Controller
{
    public function register(Request $request)
    {
        return Terminal::create([
            'terminal_uuid' => \Ramsey\Uuid\Uuid::uuid4()->toString(),
            'name' => $request->name,
            'token' => password_hash($request->name, PASSWORD_BCRYPT)
        ]);
    }
}
