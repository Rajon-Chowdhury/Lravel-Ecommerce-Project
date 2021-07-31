<?php

namespace App\Http\Controllers\Front;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Category;
use App\Product;
use App\ProductsAttribute;
use App\Cart;
use Session;
use Auth;

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
        $productDetails = Product::with(['category','brand','attributes'=>function($query){
            $query->where('status',1);
        },'images'])->find($id)->toArray();
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
        $getDiscountedAttrPrice = Product::getDiscountedAttrPrice($data['product_id'],$data['size']);
        return $getDiscountedAttrPrice;
        }
    }

    //
    public function addtocart(Request $request){
       $data = $request->all();
       //echo "<pre>" ; print_r($data); die;

       //Get Product Stock is available or not 
       $getProductStock = ProductsAttribute::where(['product_id'=>$data['product_id'],'size'=>$data['size']])->first()->toArray();
        if($getProductStock['stock']<$data['quantity']){
            $message = "Required quantity is not available";
            session::flash('error_message',$message);
            return redirect()->back(); 
        }
        //Generate session Id if not exists
        $session_id = Session::get('session_id');
        if(empty($session_id)){
            $session_id = Session::getId();
            Session::put('session_id',$session_id);
        }
        //Check product is already exists in Cart 

        if(Auth::check()){
            //If user is logged in
            $countProducts = Cart::where(['product_id'=>$data['product_id'],'size'=>$data['size'],'user_id'=>Auth::user()->id])->count();
        }else{
            //If user is not logged in
            $countProducts = Cart::where(['product_id'=>$data['product_id'],'size'=>$data['size'],'session_id'=>$session_id])->count();
        }
        
        if($countProducts>0){
           $message = "Product is Already exixts in Cart!";
            session::flash('error_message',$message);
            return redirect()->back(); 
        }

        if(Auth::check()){
            $user_id = Auth::user()->id;
        }else{
            $user_id = 0;
        }
        // Save Product in Carts Table
        // Cart::insert(['session_id'=>$session_id,'product_id'=>$data['product_id'],'size'=>$data['size'],'quantity'=>$data['quantity']]);
        $cart = new Cart;
        $cart->session_id = $session_id;
        $cart->user_id = $user_id;
        $cart->product_id = $data['product_id'];
        $cart->size =  $data['size'];
        $cart->quantity = $data['quantity'];
        $cart->save();

        $message = "Product has been added in Cart!";
        session::flash('success_message',$message);
        return redirect('cart');
    }

    // 
    public function cart(){
        $userCartItems = Cart::userCartItems();
        // dd($userCartItems); die;
        return view('front.products.cart')->with(compact('userCartItems'));
    }

    //Update Cart item Quantity using ajax funtion 
    public function updateCartItemQty(Request $request){
        if($request->ajax()){
            $data = $request->all();
            //Get Cart Details
            $cartDetails = Cart::find($data['cartid']);
            //Get Available Product Stock
            $availableStock = ProductsAttribute::select('stock')->where(['product_id'=>$cartDetails['product_id'],'size'=>$cartDetails['size']])->first()->toArray();
            //Check Stock is available or not 
            if($data['qty']>$availableStock['stock']){
                 $userCartItems = Cart::userCartItems();
                 return response()->json([
                    'status'=>false,
                    'message'=>'Product Stock is not available',
                    'view'=>(string)View::make('front.products.cart_items')->with(compact('userCartItems'))
                 ]);
            }
            //Check Size is available or not
            $availableSize = ProductsAttribute::where(['product_id'=>$cartDetails['product_id'],'size'=>$cartDetails['size'],'status'=>1])->count();
            if($availableSize==0){
                $userCartItems = Cart::userCartItems();
                 return response()->json([
                    'status'=>false,
                    'message'=>'Product Size is not available',
                    'view'=>(string)View::make('front.products.cart_items')->with(compact('userCartItems'))
                 ]);
            }
            Cart::where('id',$data['cartid'])->update(['quantity'=>$data['qty']]);
            $userCartItems = Cart::userCartItems();

            return response()->json([
                'status'=>true,
                'view'=>(string)View::make('front.products.cart_items')->with(compact('userCartItems'))]);
        }

    }
    //Delete Cart Item
    public function deleteCartItem(Request $request){
        if($request->ajax()){
            $data = $request->all();
            Cart::where('id',$data['cartid'])->delete();
            $userCartItems = Cart::userCartItems();

            return response()->json([
                'view'=>(string)View::make('front.products.cart_items')->with(compact('userCartItems'))]);
        }
    }

}
