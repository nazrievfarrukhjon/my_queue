<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'number',
        'comment',
        'category_id',
        'service_id',
        'status_id',
        'client_id',
        'user_id',
        'created_at'
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    /**
     * @param Model $service
     * @return string
     */
    public static function getNumber(Model $service): string
    {
        $count = Ticket::query()
            ->where('created_at', '>=', Carbon::now()->toDateString() . " 00:00:00")
            ->where('service_id', $service->id)
            ->count();

        $count++;

        return "{$service->code}-{$count}";
    }

    public static function getTodays()
    {
        $today = Carbon::now()->toDateString();

        $count = Ticket::where('created_at', '>=', $today)->count();

        return $count;
    }

    public static function getTodaysByUser($user)
    {
        $today = Carbon::now()->toDateString();

        $count = Ticket::where('created_at', '>=', $today)
            ->where('user_id', $user->id)
            ->count();

        return $count;
    }

}
