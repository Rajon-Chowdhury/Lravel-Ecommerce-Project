<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Banner;
use Session;


class BannersController extends Controller
{
    //
    public function banners(){
        $banners = Banner::get()->toArray();
        //dd($banners); die;
        return view('admin.banners.banners')->with(compact('banners')); 
    }

    public function updateBannerStatus(Request $request){
        if($request->ajax()){
            $data = $request->all();
          //echo "<pre>"; print_r($data); die;
           if($data['status']=="Active"){
             $status = 0;
           }
           else{
             $status = 1;
           }
           Banner::where('id',$data['banner_id'])->update(['status'=>$status]);
           return response()->json(['status'=>$status,'banner_id'=>$data['banner_id']]);
        }
    
    }
    public function deleteBanner($id){
        //Get Banner Image
        $bannerImage = Banner::where('id',$id)->first();

        //Get Banner Image Path
        $banner_image_path = 'images/banner_images/';

        //Delete Banner Image if exists in banner folder
        if(file_exists($banner_image_path.$bannerImage->image)){
            unlink($banner_image_path.$bannerImage->image);
        }
        //Delete Banner from banners table 
        Banner::where('id',$id)->delete();

        session::flash('success_message','Banner deleted Successfully!');
        return redirect()->back();
    }
}
