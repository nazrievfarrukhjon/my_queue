<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Monitor;
use App\Models\Service;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketsController extends Controller
{

   public function create(Request $request) {
//       1. Номер телефона
//2. ФИО(одно стринг поле) так как юзеру вряд ли будет норм пол часа набирать, и зависимо от того он наберет только имя или имя + инициалы свои или что то еще будет заполнено поле фио
//3. К кому он занял очередь
//4. Время и вот эти остолные поля

       $client = Client::where('phone', $request->phone)->first();
       $service = Service::find($request->service_id);
       // user укажет сам клиента или как то мы сами оперделим?
       //$user = User::find($request->user_id);
       //
       $monitor = Monitor::find($request->monitor_id)->first();
       $monitor->counter++;
       $monitor->save();

       $ticket = Ticket::create([
           'client_id' => $client->id,
           'service_id' => $service->id,
           'user_id' => $request->user_id,
           'monitor_id' => $request->monitor_id,
           'number' => $monitor->counter,
       ]);


   }

}
