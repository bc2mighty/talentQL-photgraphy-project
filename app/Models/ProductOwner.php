<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOwner extends Model
{
    use HasFactory;
    protected $keyType = 'string';
    public $incrementing = false;
    protected $primaryKey = 'id';

    public function products() {
        return $this->hasManyThrough(Product::class, ProductPhotograph::class);
    }

    public function unapprovedPhotographs() {
        return $this->hasManyThrough(ProductPhotograph::class, Product::class)->select('thumbnails', 'products.title')->where([['approved', 0], ['products.in_processing_facility', 1]]);
    }

    public function approvedPhotographs() {
        return $this->hasManyThrough(ProductPhotograph::class, Product::class)->select('thumbnails', 'products.title')->where('approved', 1);
    }
}
