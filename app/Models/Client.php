<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Client extends Model
{
    protected $fillable = [
        'phone',
        'surname',
        'name',
        'second_name',
        'tin',
        'passport',
        'address',
        'date_of_birth',
    ];

    protected array $dates = [
        'created_at',
        'updated_at',
    ];

    protected $appends = ['full_name', 'common_name'];

    public $timestamps = true;

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function getFullNameAttribute(): string
    {
        $fullName = $this->surname . " " . $this->name . " " . $this->second_name;
        return trim($fullName) != "" ? trim($fullName) : "Клиент (ФИО не указано)";
    }

    public function getCommonNameAttribute(): string
    {
        $fullName = $this->surname . " " . Str::substr($this->name, 0, 1) . ". " . Str::substr($this->second_name, 0, 1) . ".";
        return trim($fullName) != ". ." ? trim($fullName) : "Новый ({$this->phone})";
    }
}
