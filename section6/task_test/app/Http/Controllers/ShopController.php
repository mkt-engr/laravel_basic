<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Shop;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    //
    public function index()
    {
        //主->従
        $area_tokyo = Area::find(1)->shops;

        //主<-従
        $shop = Shop::find(2)->area->name;

        dd($area_tokyo, $shop);
    }
}
