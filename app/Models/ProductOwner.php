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
        return $this->hasMany(Product::class);
    }
}
