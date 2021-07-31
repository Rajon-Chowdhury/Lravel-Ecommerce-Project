<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Auth;
use Session;
use App\Cart;
use App\Sms;

class UsersController extends Controller
{
    //
    public function loginRegister(){
        return view('front.users.login_register');
    }
    public function registerUser(Request $request){
       if($request->isMethod('post')){
         $data = $request->all();
         //echo "<pre>" ; print_r($data); die;
         $userCount = User::where('email',$data['email'])->count();
         if($userCount>0){
            $message = "Email already exists";
            session::flash('error_message',$message);
            return redirect()->back();
         }else{
            $user = new User;
            $user->name    = $data['name'];
            $user->mobile  = $data['mobile'];
            $user->email   = $data['email'];
            $user->password= bcrypt($data['password']);
            $user->save();
            if(Auth::attempt(['email' => $data['email'], 'password' => $data['password']])){
             //Update user Cart with user Id
             if(!empty(Session::get('session_id'))){
                $user_id = Auth::user()->id;
                $session_id = Session::get('session_id');
                Cart::where('session_id',$session_id)->update(['user_id'=>$user_id]);
             }
              $message = "Dear Customer, You have been successfully Registerd";
              $mobile = $data['mobile'];
              Sms::sendSma($message,$mobile);

              return redirect('casual-t-shirt');
            }
         }
       }
    }
    public function checkEmail(Request $request){
        //Check if email is alreay exists
        $data = $request->all();
        $emailCount = User::where('email',$data['email'])->count();

        if($emailCount>0){
            return "false";
        }else{
            return "true";
        }
    }
    public function loginUser(Request $request){
        if($request->isMethod('post')){
            $data = $request->all();

           if(Auth::attempt(['email' => $data['email'], 'password' => $data['password']])){
              //Update user Cart with user Id
             if(!empty(Session::get('session_id'))){
                $user_id = Auth::user()->id;
                $session_id = Session::get('session_id');
                Cart::where('session_id',$session_id)->update(['user_id'=>$user_id]);
             }
              return redirect('/cart');
            }else{
                $message = "Invalid Username or Password!";
                session::flash('error_message',$message);
                return redirect()->back();
            }
        }

    }

    public function logoutUser(){
        Auth::logout();
         
        return redirect('/'); 
    }
}
