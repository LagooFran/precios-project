<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public $name;
    public $price;
    public $storeId;
    public $img;

    public function __construct(array $attributes = []){
        $this->name = $attributes["name"];
        $this->price = $attributes["price"];
        $this->storeId = $attributes["storeId"];
        $this->img = $attributes["img"];
    }


}
