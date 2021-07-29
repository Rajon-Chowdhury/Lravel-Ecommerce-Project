<?php

namespace App\Http\Controllers\Front;
use Illuminate\Support\Facades\Route;
use Illuminate\Pagination\Paginator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Category;
use App\Product;
use App\ProductsAttribute;

class ProductsController extends Controller
{
    //
    //Listing Page
    public function listing(Request $request){
        Paginator::useBootstrap();
        if($request->ajax()){
            $data = $request->all();
            //echo "<pre>"; print_r($data); die;
            $url = $data['url'];
            $categoryCount = Category::where(['url'=>$url,'status'=>1])->count();
            if($categoryCount>0){
               // echo "Category exists"; die;
                $categoryDetails = Category::catDetails($url);
                //echo "<pre>"; print_r($categoryDetails); die;
                $categoryProducts = Product::with('brand')->whereIn('category_id',$categoryDetails['catIds'])->where('status',1);

            //If fabric Filter is selected

             if(isset($data['fabric']) && !empty($data['fabric'])){
                 $categoryProducts->whereIn('products.fabric',$data['fabric']);
                 //products.fabric means selecting fabric colum from products table
             }   

            //If sleeve Filter is selected

             if(isset($data['sleeve']) && !empty($data['sleeve'])){
                 $categoryProducts->whereIn('products.sleeve',$data['sleeve']);
                 //products.sleeve means selecting sleeve colum from products table
             }   
            //If pattern Filter is selected

             if(isset($data['pattern']) && !empty($data['pattern'])){
                 $categoryProducts->whereIn('products.pattern',$data['pattern']);
                 //products.pattern means selecting pattern colum from products table
             }   
            //If fit Filter is selected

             if(isset($data['fit']) && !empty($data['fit'])){
                 $categoryProducts->whereIn('products.fit',$data['fit']);
                 //products.fit means selecting fit colum from products table
             }   
            //If occasion Filter is selected

             if(isset($data['occasion']) && !empty($data['occasion'])){
                 $categoryProducts->whereIn('products.occasion',$data['occasion']);
                 //products.occasion means selecting occasion colum from products table
             }                                          


            //If sort option is selected by user
              if(isset($data['sort']) && !empty($data['sort'])){
                if($data['sort']=="product_latest"){
                    $categoryProducts->orderBy('id','Desc');
                }
                else if($data['sort']=="product_name_a_z"){
                    $categoryProducts->orderBy('product_name','Asc');
                }
                else if($data['sort']=="product_name_z_a"){
                    $categoryProducts->orderBy('product_name','Desc');
                }
                else if($data['sort']=="price_lowest"){
                    $categoryProducts->orderBy('product_price','Asc');
                }
                else if($data['sort']=="price_highest"){
                    $categoryProducts->orderBy('product_price','Desc');
                }
             }
             else{
                    $categoryProducts->orderBy('id','Desc');
             }   
             $categoryProducts = $categoryProducts->paginate(3);

              return view('front.products.ajax_products_listing')->with(compact('categoryDetails','categoryProducts','url'));  
            }
            else{
                abort(404);
            }

        }
        else{
            $url = Route::getFacadeRoot()->current()->uri();
            $categoryCount = Category::where(['url'=>$url,'status'=>1])->count();
            if($categoryCount>0){
               // echo "Category exists"; die;
                $categoryDetails = Category::catDetails($url);
                //echo "<pre>"; print_r($categoryDetails); die;
                $categoryProducts = Product::with('brand')->whereIn('category_id',$categoryDetails['catIds'])->where('status',1);

              $categoryProducts = $categoryProducts->paginate(3);

                   //Product  filters
                   $productFilters = Product::productFilters();
                   $fabricArray = $productFilters['fabricArray'];
                   $sleeveArray = $productFilters['sleeveArray'];
                   $patternArray = $productFilters['patternArray'];
                   $fitArray = $productFilters['fitArray'];
                   $occasionArray = $productFilters['occasionArray'];  
       
              $page_name = "listing";
              return view('front.products.listing')->with(compact('categoryDetails','categoryProducts','url','fabricArray','sleeveArray','patternArray','fitArray','occasionArray','page_name'));  
            }
            else{
                abort(404);
            }
            }
    }
    //Product Detail page
    public function detail($id){
        $productDetails = Product::with('category','brand','attributes','images')->find($id)->toArray();
        //dd($productDetails);die;

        $total_stock = ProductsAttribute::where('product_id',$id)->sum('stock');
        $relatedProducts = Product::where('category_id',$productDetails['category']['id'])->where('id','!=',$id)->limit(3)->inRandomOrder()->get()->toArray();
        //dd($relatedProducts);die;
        
        
        return view('front.products.detail')->with(compact('productDetails','total_stock','relatedProducts'));
    }

    public function getProductPrice(Request $request){
        if($request->ajax()){
            $data = $request->all();
            // echo "<pre>"; print_r($data); die;
            $getProductPrice = ProductsAttribute::where(['product_id'=>$data['product_id'],'size'=>$data['size']])->first();

        return $getProductPrice->price;
        }
    }
}
