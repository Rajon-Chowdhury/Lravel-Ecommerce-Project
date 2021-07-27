<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Category;
use App\Section;
use Session;
use Image;

class CategoryController extends Controller
{
    //
    public function categories(){
       Session::put('page','categories');
       $categories = Category::with(['section','parentcategory'])->get();
       // $categories = json_decode(json_encode($categories));
       // echo "<pre>"; print_r($categories);

       return view('admin.categories.categories')->with(compact('categories')); 
    }
   public function updateCategoryStatus(Request $request){
    	if($request->ajax()){
    		$data = $request->all();
    	  //echo "<pre>"; print_r($data); die;
           if($data['status']=="Active"){
           	 $status = 0;
           }
           else{
           	 $status = 1;
           }
           Category::where('id',$data['category_id'])->update(['status'=>$status]);
           return response()->json(['status'=>$status,'category_id'=>$data['category_id']]);
    	}
    }
    public function addEditCategory(Request $request,$id=null){
    	if($id==""){
    		$title = "Add Category";
    		// Add Category Functionality
    		$category = new Category;
    		$categorydata = array();
    		$getCategories =array();
    		$message = 'Category Added Successfully';
    	}else{
    	   // Edit Category Functionality	
    		$title = "Edit Category";
    		$categorydata = Category::where('id',$id)->first();
    		$categorydata = json_decode(json_encode($categorydata),true);
    		$getCategories = Category::with('subcategories')->where(['parent_id'=>0,'section_id'=>$categorydata['section_id']])->get();
    		$getCategories =  json_decode(json_encode($getCategories),true);
    		//echo "<pre>"; print_r($getCategories); die;
    		$category = Category::find($id);
            $message = 'Category Updated Successfully';
    	}
        if($request->isMethod('post')){
        	$data = $request->all();
        	//echo "<pre>"; print_r($data); die;
          
          //Category validation
            $rules = [
	        'category_name' => 'required|regex:/^[\pL\s\-]+$/u',
	        'section_id'=>'required',
	        'url'=>'required',
	        'category_image' =>'image',
	        ];
	        $customMessages = [
	         'category.required' =>'Category Name is required',
	         'category.regex' =>'Valid Category Name is required',
	         'section_id.required' =>'Section ID is required',
	         'url.required'=>'Category URL is required',
	         'category_image.image'=>'Valid Image is required'	,         
	        ];	        
           $this->validate($request,$rules,$customMessages);
           
            
        	//Upload category Image
        	if($request->hasFile('category_image')){
           	$image_tmp = $request->file('category_image');
           	 if($image_tmp->isValid()){
           	 	//Get Image Extention
           	 	$extension = $image_tmp->getClientOriginalExtension();
           	 	//Generate New image name
           	 	$imageName = rand(111,99999).'.'.$extension;
           	 	$imagePath = 'images/category_images/'.$imageName;
           	 	//upload the images
           	 	Image::make($image_tmp)->resize(300,400)->save($imagePath);
           	 	$category->category_image = $imageName;
           	 }
           	}

           	if(empty($data['category_discount'])){
           		$data['category_discount'] = "";
           	} 
           	if(empty($data['description'])){
           		$data['description'] = "";
           	} 
           	if(empty($data['meta_title'])){
           		$data['meta_title'] = "";
           	} 
          	if(empty($data['meta_description'])){
           		$data['meta_description'] = "";
           	} 
           	if(empty($data['meta_keywords'])){
           		$data['meta_keywords'] = "";	
           	} 
           	//echo "<pre>"; print_r($data); die;
        	$category->parent_id = $data['parent_id'];
        	$category->section_id = $data['section_id'];
        	$category->category_name = $data['category_name'];
        	$category->category_discount = $data['category_discount'];
        	$category->description = $data['description'];
        	$category->url = $data['url'];
        	$category->meta_title = $data['meta_title'];
        	$category->meta_description = $data['meta_description'];
        	$category->meta_keywords = $data['meta_keywords'];
        	$category->status = 1;
        	$category->save();
            
            Session::flash('success_message',$message);
            return redirect('admin/categories');
        }
    	// Get all section Here
    	$getSections = Section::get();
    	return view('admin.categories.add_edit_category')->with(compact('title','getSections','categorydata','getCategories'));

    }
    public function appendCategoryLevel(Request $request){

           if($request->ajax()){
           	$data = $request->all();
           	//echo "<pre>"; print_r($data); die;
           	$getCategories = Category::with('subcategories')->where(['section_id'=>$data['section_id'],'parent_id'=>0,'status'=>1])->get();
           	$getCategories = json_decode(json_encode($getCategories),true);
           //	echo "<pre>"; print_r($getCategories); die;

           return view('admin.categories.append_categories_level')->with(compact('getCategories'));	
           }
    }
    public function deleteCategoryImage($id){
         //Get Category Image
    	$categoryImage = Category::select('category_image')->where('id',$id)->first();
        
    	// Get Category Image Path
    	$category_image_path = 'images/category_images/';

    	//Delete Category Images from categories images folder if exists
        if(file_exists($category_image_path.$categoryImage->category_image)){
        	unlink($category_image_path.$categoryImage->category_image);
        }

        //Delete Category Images from categories table 
         Category::where('id',$id)->update(['category_image'=>'']);
         $message = 'Category Image has been deleted Successfully'; 
        //return redirect()->back()->with('flash_message_success','Category Image has been deleted Successfully!');
         Session::flash('success_message',$message);
         return redirect()->back();

    }
    public function deleteCategory($id){
    	Category::where('id',$id)->delete();
    	$message = 'Category  has been deleted Successfully'; 
    	Session::flash('success_message',$message);
    	return redirect()->back();

    }
   
}
