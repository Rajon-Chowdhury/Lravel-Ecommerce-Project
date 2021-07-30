<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Product;

class IndexController extends Controller
{
    //
    public function index(){
        //Get featured Items
         $featuredItemsCount = Product::where(['is_featured'=>'Yes','status'=>1])->count();
         $featuredItems = Product::where(['is_featured'=>'Yes','status'=>1])->get()->toArray();
         $featuredItemsChunk = array_chunk($featuredItems, 3);
         //echo "<pre>" ; print_r($featuredItemsChunk) ; die;
         //dd($featuredItems); die;

        //Get Latest Product
        $newProducts = Product::orderBy('id','Desc')->where('status',1)->limit(3)->get()->toArray();
        // echo "<pre>" ; print_r($newProduct) ; die;
                
        $page_name = "index";
        return view('front.index')->with(compact('page_name','featuredItemsChunk','featuredItemsCount','newProducts'));
    }
}
