<?php
namespace App\Controllers;

use \App\Models\Product;
use \TypeRocket\Controllers\WPPostController;

class ProductController extends WPPostController
{
    protected $modelClass = Product::class;
}
