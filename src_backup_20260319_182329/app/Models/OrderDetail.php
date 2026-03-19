<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $fillable = [
        'order_id',
        'service_id',
        'service_option_id',
        'quantity',
        'unit_price',
        'total_price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    /**
     * 注文とのリレーション
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * サービスとのリレーション
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * サービスオプションとのリレーション
     */
    public function serviceOption()
    {
        return $this->belongsTo(ServiceOption::class);
    }
}
