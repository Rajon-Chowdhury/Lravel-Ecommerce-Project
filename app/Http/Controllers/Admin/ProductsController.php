<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Product;
use App\Section;
use App\Category;
use Session;
use Image;
use App\ProductsAttribute;
use App\ProductsImage;

class ProductsController extends Controller
{
    //
    public function products(){
        Session::put('page','product s');
        //Normal query which fetch all data from diff table
        /* $products = Product::with(['category','section'])->get();
        */
        //Using this subquery we only use needed data
           $products = Product::with(['category'=>function($query){
              $query->select('id','category_name');
           },'section'=>function($query){
              $query->select('id','name');
           }])->get();

        // $products = json_decode(json_encode($products));
        // echo "<pre>"; print_r($products); die;

        return view('admin.products.products')->with(compact('products'));
    }

   public function updateProductStatus(Request $request){
    if($request->ajax()){
        $data = $request->all();
      //echo "<pre>"; print_r($data); die;
       if($data['status']=="Active"){
         $status = 0;
       }
       else{
         $status = 1;
       }
       Product::where('id',$data['product_id'])->update(['status'=>$status]);
       return response()->json(['status'=>$status,'product_id'=>$data['product_id']]);
    }
  }

    // add and edit product
    public function addEditProduct(Request $request,$id=null){
        if($id==""){
            $title = "Add Product";
            $product = new Product;
            $productdata = array();
            $message = 'Product Added Successfully';
            // Add Category Functionality
           
        }else{
           // Edit Category Functionality   
            $title = "Edit Product";
            $productdata = Product::find($id);
            $productdata = json_decode(json_encode($productdata),true);
           
            $message = 'Product Updated Successfully';
           
        }
    if($request->isMethod('post')){
        $data = $request->all();
        //echo "<pre>"; print_r($data['is_featured']); die;
        //echo "<pre>"; print_r($productdata['is_featured']); die;
       
     //Product Validation
        $rules = [
        'category_id'=>'required',
        'product_name' => 'required|regex:/^[\pL\s\-]+$/u',
        'product_code' => 'required|regex:/^[\w-]*$/',
        'product_price'=>'required|numeric',
        'product_color' => 'required|regex:/^[\pL\s\-]+$/u',
        ];
        $customMessages = [
         'category_id.required' =>'Category is required',
         'product_name.required' =>'Product Name is required',
         'product_name.regex' =>'Valid Product Name is required',
         'product_code.required' =>'Product Code is required',
         'product_code.regex' =>'Valid Product Code is required', 
         'product_price.required' =>'Product Price is required',
         'product_price.numeric' =>'Valid Product Price is required', 
         'product_color.required' =>'Product Color is required',
         'product_color.regex' =>'Valid Product Color is required',         
        ];          
       $this->validate($request,$rules,$customMessages);

       if(!empty($data['is_featured'])){
          $is_featured = $data['is_featured'];
       }else{
          $is_featured = "No";
       }


       if(empty($data['discount'])){
          $data['discount'] ="";
       }       
       if(empty($data['weight'])){
          $data['weight'] ="";
       }       
       if(empty($data['product_video'])){
          $data['product_video'] ="";
       }       
       if(empty($data['description'])){
          $data['description'] ="";
       }       
       if(empty($data['wash_care'])){
          $data['wash_care'] ="";
       }
       if(empty($data['fabric'])){
          $data['fabric'] ="";
       }
       if(empty($data['pattern'])){
          $data['pattern '] ="";
       }
      if(empty($data['sleeve'])){
          $data['sleeve'] ="";
       }   
       if(empty($data['fit'])){
          $data['fit '] ="";
       }
       if(empty($data['occasion'])){
          $data['occasion'] ="";
       }
    
       if(empty($data['meta_title'])){
          $data['meta_title'] ="";
       }       
       if(empty($data['meta_description'])){
          $data['meta_description'] ="";
       }       
       if(empty($data['meta_keywords'])){
          $data['meta_keywords'] ="";
       }
        //Upload Product Main Image
     if($request->hasFile('main_image')){
       $image_tmp = $request->file('main_image');
     if($image_tmp->isValid()){
        //Get Image Extention
        $image_name = $image_tmp->getClientOriginalName();
        $extension = $image_tmp->getClientOriginalExtension();

        //Generate New image name
        $imageName = $image_name.'-'.rand(111,99999).'.'.$extension;
        $large_image_path = 'images/product_images/large/'.$imageName;
        $medium_image_path = 'images/product_images/medium/'.$imageName;
        $small_image_path = 'images/product_images/small/'.$imageName;

        //upload the images
        Image::make($image_tmp)->save($large_image_path);
        //Upload small and medium image after resize
        Image::make($image_tmp)->resize(520,600)->save($medium_image_path);
        Image::make($image_tmp)->resize(260,300)->save($small_image_path);
        $product->main_image = $imageName;
     }
     }
     // upload product video
     if($request->hasFile('product_video')){
        $video_tmp = $request->file('product_video');
        if($video_tmp->isValid()){
            // upload video
            //Get the orginal video Name
            $video_name = $video_tmp->getClientOriginalName();
            //Get Video extention
            $extension = $video_tmp->getClientOriginalExtension();
            //Geerate New Video name  
            $videoName = $video_name.'-'.rand(111,99999).'.'.$extension;

            $video_path = 'videos/product_videos/'.$videoName;
            $video_tmp->move($video_path,$videoName);

            // save product vedio in the product table
            $product->product_video = $videoName;
        }
     }


       //Save Product details in products table
       $categoryDetails = Category::find($data['category_id']);
       $product->section_id    = $categoryDetails['section_id'];
       $product->category_id     = $data['category_id'];
       $product->product_name    = $data['product_name'];
       $product->product_code    = $data['product_code'];
       $product->product_color   = $data['product_color'];
       $product->product_price   = $data['product_price'];
       $product->product_discount= $data['product_discount'];
       $product->product_weight  = $data['product_weight'];
       $product->product_video   = $data['product_video'];
       $product->description     = $data['description'];
       $product->wash_care       = $data['wash_care'];
       $product->fabric          = $data['fabric'];
       $product->pattern         = $data['pattern'];
       $product->sleeve          = $data['sleeve'];
       $product->fit             = $data['fit'];
       $product->occasion        = $data['occasion'];
       $product->meta_title      = $data['meta_title'];
       $product->meta_description= $data['meta_description'];
       $product->meta_keywords   = $data['meta_keywords'];
       $product->is_featured     = $is_featured;
       $product->status          = 1;
       $product->save();
       Session::flash('success_message',$message);
       return redirect('admin/products');

    }
           

        //filter array 
        $fabricArray = array('Cotton','Polyester','Wool');
        $sleeveArray = array('Full Sleeve','Half Sleeve','Short Sleeve','Sleeveless');
        $patternArray = array('Checked','Plain','Printed','Self','Solid');
        $fitArray = array('Regular','Slim');
        $occasionArray = array('Casual','Formal');
        
        // Select section with catagories and subcatagories
        $categories = Section::with('categories')->get();

        $categories = json_decode(json_encode($categories),true);
        //echo "<pre>"; print_r($catagories); die;        

        return view('admin.products.add_edit_product')->with(compact('title','fabricArray','sleeveArray','patternArray','fitArray','occasionArray','categories','productdata'));

    }
    public function deleteProductImage($id){
         //Get Category Image
        $productImage = Product::select('main_image')->where('id',$id)->first();
        
        // Get product Image Path
        $small_image_path = 'images/product_images/small';
        $medium_image_path = 'images/product_images/medium';
        $large_image_path = 'images/product_images/large';

        //Delete Product Small Images  if exists in small folder
        if(file_exists($small_image_path.$productImage->main_image)){
            unlink($small_image_path.$productImage->main_image);
        }      
        //Delete Product medium Images  if exists in medium folder
        if(file_exists($medium_image_path.$productImage->main_image)){
            unlink($medium_image_path.$productImage->main_image);
        }        
        //Delete Product Large Images  if exists in Large folder
        if(file_exists($large_image_path.$productImage->main_image)){
            unlink($large_image_path.$productImage->main_image);
        }

        //Delete product Images from products table 
         Product::where('id',$id)->update(['main_image'=>'']);
         $message = 'Product Image has been deleted Successfully'; 
        //return redirect()->back()->with('flash_message_success','product Image has been deleted Successfully!');
         Session::flash('success_message',$message);
         return redirect()->back();

    }
    public function deleteProductVideo($id){
     //Get Category Video
    $productVideo = Product::select('product_video')->where('id',$id)->first();
    
    // Get Product Video Path
    $product_video_path = 'videos/product_videos/';

    //Delete Product Videos from product_videos folder if exists
    if(file_exists($product_video_path.$productVideo->product_video)){
        unlink($product_video_path.$productVideo->product_video);
    }

    //Delete Product Videos from product table 
     Product::where('id',$id)->update(['product_video'=>'']);
     $message = 'Product Video has been deleted Successfully'; 
    //return redirect()->back()->with('flash_message_success','Product Video has been deleted Successfully!');
     Session::flash('success_message',$message);
     return redirect()->back();

    }

    //Delete Prodcut
    public function deleteProduct($id){
        Product::where('id',$id)->delete();
        $message = 'Product  has been deleted Successfully'; 
        Session::flash('success_message',$message);
        return redirect()->back();

    }

    //
    public function addAttributes(Request $request,$id)
    {
        if($request->isMethod('post')){
            $data = $request->all();
            //echo "<pre>"; print_r($data); die;
            foreach ($data['sku'] as $key => $value) {
               if(!empty($value)){ 
               $attrCountSKU = ProductsAttribute::where('sku',$value)->count();
               if($attrCountSKU>0){
                $message = 'SKU already exists.Please add another Size!';
                session::flash('error_message',$message);
                return redirect()->back();
               } 
               $attrCountSize = ProductsAttribute::where(['product_id'=>$id,'size'=>$data['size'][$key]])->count();
               if($attrCountSize>0){
                $message = 'Size already exists.Please add another SKU!';
                session::flash('error_message',$message);
                return redirect()->back();
               } 
               $attribute = new ProductsAttribute;
               $attribute->product_id = $id;
               $attribute->sku =  $value;
               $attribute->size = $data['size'][$key];
               $attribute->price = $data['price'][$key];
               $attribute->stock = $data['stock'][$key];
               $attribute->status = 1;
               $attribute->save();

              }
                
            }
            $success_message = 'Product Attributes added Successfully!';
            session::flash('success_message',$success_message);
            return redirect()->back();            
        }
        $productdata = Product::select('id','product_name','product_code','product_color','main_image')->with('attributes')->find($id);
        $productdata = json_decode(json_encode($productdata),true);
        //echo "<pre>"; print_r($productdata); die;
        $title = "Product Attributes";
        return view('admin.products.add_attributes')->with(compact('productdata','title'));
    }
    public function editAttributes(Request $request,$id)
    {
        if($request->isMethod('post')){
            $data = $request->all();
            //echo "<pre>"; print_r($data); die;
            foreach ($data['attrId'] as $key => $attr) {
                if(!empty($attr)){
                    ProductsAttribute::where(['id'=>$data['attrId'][$key]])->update(['price'=>$data['price'][$key],'stock'=>$data['stock'][$key]]);
                }
                
            }
            $success_message = 'Product Attributes updated Successfully!';
            session::flash('success_message',$success_message);
            return redirect()->back();   
        }        
    }
    public function updateAttributeStatus(Request $request)
    {
    if($request->ajax()){
        $data = $request->all();
      //echo "<pre>"; print_r($data); die;
       if($data['status']=="Active"){
         $status = 0;
       }
       else{
         $status = 1;
       }
       ProductsAttribute::where('id',$data['attribute_id'])->update(['status'=>$status]);
       return response()->json(['status'=>$status,'attribute_id'=>$data['attribute_id']]);
    }
   }
    public function updateImageStatus(Request $request)
    {
    if($request->ajax()){
        $data = $request->all();
      //echo "<pre>"; print_r($data); die;
       if($data['status']=="Active"){
         $status = 0;
       }
       else{
         $status = 1;
       }
       ProductsImage::where('id',$data['image_id'])->update(['status'=>$status]);
       return response()->json(['status'=>$status,'image_id'=>$data['image_id']]);
    }
   }
    //Delete Attribute
    public function deleteAttribute($id){
        ProductsAttribute::where('id',$id)->delete();
        $message = 'Product Attribute  has been deleted Successfully'; 
        Session::flash('success_message',$message);
        return redirect()->back();

    }
    public function addImages(Request $request,$id){
       
       if($request->isMethod('post')){
          $data = $request->all();
          
          if($request->hasFile('images')){
            $images = $request->file('images');
            foreach ($images as $key => $image) {
                $productImage = new ProductsImage;
                $image_tmp = Image::make($image);
               // $originalName = $image->getClientOriginalName();
                $extension = $image->getClientOriginalExtension();
                $imageName = rand(111,999999).time().".".$extension;


                $large_image_path = 'images/product_images/large/'.$imageName;
                $medium_image_path = 'images/product_images/medium/'.$imageName;
                $small_image_path = 'images/product_images/small/'.$imageName;

                //upload the images
                Image::make($image_tmp)->save($large_image_path);
                //Upload small and medium image after resize
                Image::make($image_tmp)->resize(520,600)->save($medium_image_path);
                Image::make($image_tmp)->resize(260,300)->save($small_image_path);

                 $productImage->image = $imageName;
                 $productImage->product_id = $id;
                 $productImage->status = 1;

                $productImage->save();

            }
           $message = 'Product Images  has been added Successfully'; 
           Session::flash('success_message',$message);
           return redirect()->back();


          }
       }
        
       $productdata = Product::with('images')->select('id','product_name','product_code','product_color','main_image')->where('id',$id)->first();
       $productdata = json_decode(json_encode($productdata),true);
       //echo "<pre>"; print_r($productdata); die; 
       $title = "Product Image";
       return view('admin.products.add_images')->with(compact('title','productdata'));
    }

    //Delete image of ProductsImage table
    public function deleteImage($id){
     //Get Category Image
    $productImage = ProductsImage::select('image')->where('id',$id)->first();
    
    // Get product Image Path
    $small_image_path = 'images/product_images/small';
    $medium_image_path = 'images/product_images/medium';
    $large_image_path = 'images/product_images/large';

    //Delete Product Small Images  if exists in small folder
    if(file_exists($small_image_path.$productImage->image)){
        unlink($small_image_path.$productImage->image);
    }      
    //Delete Product medium Images  if exists in medium folder
    if(file_exists($medium_image_path.$productImage->image)){
        unlink($medium_image_path.$productImage->image);
    }        
    //Delete Product Large Images  if exists in Large folder
    if(file_exists($large_image_path.$productImage->image)){
        unlink($large_image_path.$productImage->image);
    }

    //Delete product Images from productsImage table 
     Product::where('id',$id)->update(['main_image'=>'']);
     $message = 'Product Images has been deleted Successfully'; 
    //return redirect()->back()->with('flash_message_success','product Image has been deleted Successfully!');
     Session::flash('success_message',$message);
     return redirect()->back();

    }
}
