<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Ingredient extends Model
{
    use HasFactory,Notifiable;
    // Notifiable here is a trait that is used to send notifications for the ingredient model.

    protected $fillable = ['name', 'stock', 'initial_stock'];

    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot('quantity');
    }
}
