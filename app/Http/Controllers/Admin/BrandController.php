<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Brand;
use Session;

class BrandController extends Controller
{
    //
    public function brands(){
        Session::put('page','brands');
        $brands = Brand::get();

        return view('admin.brands.brands')->with(compact('brands'));
    }

    public function updateBrandStatus(Request $request){
        if($request->ajax()){
            $data = $request->all();
          //echo "<pre>"; print_r($data); die;
           if($data['status']=="Active"){
             $status = 0;
           }
           else{
             $status = 1;
           }
           Brand::where('id',$data['brand_id'])->update(['status'=>$status]);
           return response()->json(['status'=>$status,'brand_id'=>$data['brand_id']]);
        }
    
    }
    public function addEditBrand(Request $request,$id=null){
       Session::put('page','brands'); 
        if($id==""){
            $title = "Add Brand";
            $brand = new Brand;
            $message = "Brand Added Successfully";
        }
        else{
            $title = "Edit Brand";
            $brand =  Brand::find($id);
            $message = "Brand Updated Successfully";
         }

         if($request->isMethod('post')){
            $data = $request->all();
          //  echo "<pre>"; print_r($data); die;

            //Brand validation
            $rules = [
            'brand_name' => 'required|regex:/^[\pL\s\-]+$/u',

            ];
            $customMessages = [
             'brand_name.required' =>'Brand Name is required',
             'brand_name.regex' =>'Valid Brand Name is required',
           
            ];          
           $this->validate($request,$rules,$customMessages);
           $brand->name = $data['brand_name'];
           $brand->status = 1;
           $brand->save();
           session::flash('success_message',$message);
           return redirect('admin/brands');
         }
         return view('admin.brands.add_edit_brand')->with(compact('title','brand'));

    }
    //Delete Brand
    public function deleteBrand($id){
        Brand::where('id',$id)->delete();
        $message = 'Brand  has been deleted Successfully'; 
        Session::flash('success_message',$message);
        return redirect()->back();

    }
}
