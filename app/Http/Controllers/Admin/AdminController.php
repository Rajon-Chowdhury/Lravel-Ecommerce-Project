<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Auth;
use App\Admin;
use Hash;
use Image;

class AdminController extends Controller
{
    //
    public function dashboard(){
    	Session::put('page','dashboard');
    	return view('admin.admin_dashboard');
    }

    public function settings(){ 
    	//echo "<pre>"; print_r(Auth::guard('admin')->user()); die;

    	/*$adminDetails = Admin::where('email',Auth::guard('admin')->user()->email()->first();
    		return view('admin.admin_settings')->with(compact('$adminDetails'));*/
        Session::put('page','settings');
    	return view('admin.admin_settings');
    }


    public function login(Request $request){
    	if($request->isMethod('post')){
    		$data = $request->all();
    		//echo "<pre>"; print_r($data);die;
    
    	$rules = [
        'email' => 'required|email|max:255',
        'password' => 'required',
        ];
        $customMessages = [
         'email.required' =>'Email is required',
         'email.email' =>'Valid Email is required',
         'password.required' =>'Password is required',
        ];
         $this->validate($request,$rules,$customMessages);

         if(Auth::guard('admin')->attempt(['email'=>$data['email'],'password'=>$data['password']])){
		 		return redirect('admin/dashboard');
		    }
		   else{
		    	Session::flash('error_message','Invalid Email or Password');
		        return redirect()->back();
		       }       
		    		
    		
    	}
    	return view('admin.admin_login');
    }
    public function logout(){
    	Auth::guard('admin')->logout();
        return redirect('/admin');
    }
    public function chkCurrentPassword(Request $request){
    	$data = $request->all();
    	//echo "<pre"; print_r($data);die; 
    	if(Hash::check($data['current_pwd'],Auth::guard('admin')->user()->password)){
    		echo "true";
    	}    
    	else{
    		echo "false";
    	}                                                                                       
    }
    public function updateCurrentPassword(Request $request){
          if($request->isMethod('post')){
             $data = $request->all();
             //echo "<pre>"; print_r($data);die; 
             //Check if current password is correct   
         if(Hash::check($data['current_pwd'],Auth::guard('admin')->user()->password)){
             //check new  and confirm password is matching
         	if($data['new_pwd']==$data['confirm_pwd']){
               Admin::where('id',Auth::guard('admin')->user()->id)->update(['password'=>bcrypt($data['new_pwd'])]);
               Session::flash('success_message','Password has been updated successfully!');
         	}
         	else{
            Session::flash('error_message','New password and confiirm password not match');
         	}
         }  
         else{
            Session::flash('error_message','Your current password is incorrect');
         }
         return redirect()->back();

          }
    }
   public function updateAdminDetails(Request $request){
   	     Session::put('page','update-admin-details');

   	     if($request->isMethod('post')){
             $data = $request->all();
          //   echo "<pre>"; print_r($data);die;
	        $rules = [
	        'admin_name' => 'required|regex:/^[\pL\s\-]+$/u',
	        'admin_mobile'=>'required|numeric',
	        'admin_image' =>'image',
	        ];
	        $customMessages = [
	         'admin_name.required' =>'Admin Name is required',
	         'admin_name.regex' =>'Valid Name is required',
	         'admin_mobile.required' =>'Mobile is required',
	         'admin_mobile.numeric'=>'Valid Mobile is required',
	         'admin_image.image'=>'Valid Image is required'	,         
	        ];	        
           $this->validate($request,$rules,$customMessages);
           
           //upload admin image
           if($request->hasFile('admin_image')){
           	$image_tmp = $request->file('admin_image');
           	 if($image_tmp->isValid()){
           	 	//Get Image Extention
           	 	$extension = $image_tmp->getClientOriginalExtension();
           	 	//Generate New image name
           	 	$imageName = rand(111,99999).'.'.$extension;
           	 	$imagePath = 'images/admin_images/admin_photos/'.$imageName;
           	 	//upload the images
           	 	Image::make($image_tmp)->resize(300,400)->save($imagePath);
           	 }else if(!empty($data['current_admin_image'])){
           	 	$imageName = $data['current_admin_image'];
           	 }else{
           	 	$imageName = "";
           	 }
           }
  

           //update Admin Details
           Admin::where('email',Auth::guard('admin')->user()->email)
           ->update(['name'=>$data['admin_name'],'mobile'=>$data['admin_mobile'],'image'=>$imageName]);
           Session::flash('success_message','Admin details updated successfully!');

           return redirect()->back();
         }
   	 return view('admin.update_admin_details');
   }  
}
