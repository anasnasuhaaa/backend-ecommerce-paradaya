<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Products extends Model
{
    use HasFactory, HasUuids;
    protected $table = 'products';

    protected $fillable = ['name', 'price', 'description', 'image', 'stock', 'category_id'];

    public function listProducts()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    public function listOrders(){
        return $this->hasMany(Orders::class, 'product_id');
    }
}
