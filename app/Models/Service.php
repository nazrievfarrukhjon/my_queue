<?php

namespace App\Models;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'boj',
        'code',
        'hizmat',
        'kogaz',
        'category_id'
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public static function boot(): void
    {
        parent::boot();

        self::created(static function ($model){
            if (!$model->code) {
                $model->code = 'SRV' . $model->id;
            }
        });
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class);
    }
}
