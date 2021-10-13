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

        //多：多
        $shop_route = Shop::find(1)->route()->get();
        dd(Shop::find(1), Shop::find(1)->route()->get());
        dd($area_tokyo, $shop, $shop_route);
    }
}
