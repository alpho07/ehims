<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends BaseModel
{
    use HasFactory;

    protected $fillable = ['facility_id', 'month', 'year'];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }


    public static function orderExists($facilityId, $month, $year): bool
    {
        return Order::where('facility_id', $facilityId)
            ->where('month', $month)
            ->where('year', $year)
            ->exists();
    }

}
