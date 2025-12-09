<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceOption extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'service_id',
        'name',
        'description',
        'price',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * サービスとのリレーション
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
