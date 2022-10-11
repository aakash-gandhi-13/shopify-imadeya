<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryProduct extends Model
{
    use HasFactory;
    protected $table = 'categories_products';
    protected $fillable = [
        'product_id', 'category_id', 'created_by', 'updated_by', 'created_at', 'updated_at'
    ];
}
