<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use constGuards; 
use constDefaults;
use App\Models\Admin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;


class adminController extends Controller
{
   public function loginHandlerAjax(Request $request){
    $fieldType = filter_var($request->login_id, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

    if ($fieldType == 'email'){
        $request->validate([
            'login_id'=>'required|email|exists:admin,email',
            'password'=>'required|min:5|max:45'
        ],[
            'login_id.required'=>'Email or username is required',
            'login_id.email'=>'Invalid email address',
            'login_id.exists'=>'Email is not existing',
            'password.required'=>'Password is required'
        ]);

       
    }else{
        $request->validate([
        'login_id'=>'required|exists:admin,username',
        'password'=>'required|min:5|max|45'
        ],[
            'login_id.required'=>'Email or username is required',
            'login_id.exists'=>'Username is not existing',
            'password.required'=>'Password is required'
        ]);
      }
      $creds = [
        $fieldType => $request->login_id,
        'password' => $request->password
    ];

    if(Auth::guard('admin')->attempt($creds)) {
        return response()->json(['success' => true, 'redirect_url' => route('admin.home')]);
    } else {
        return response()->json(['success' => false, 'message' => 'Incorrect credentials'], 401);
    }
}
   public function logoutHandler (Request $request){
    Auth::guard('admin')->logout();
    session()->flash('fail','You are Logged Out');
    return redirect()->route('admin.login');
   }

   public function sendPasswordResetLink (Request $request){
    $request->validate([
      'email'=>'required|email|exists:admin,email'
    ],[
      'email.required'=>'The :attribute is required',
      'email.email'=>'Invalid email',
      'email.exists'=>'This :attribute does not exists in the system'
    ]);

    //admin details
    $admin = Admin::where('email',$request->email)->first();

    //generate token
    $token = base64_encode(Str::random(64));

    $oldToken = DB::table('password_reset_tokens')
                  ->where(['email'=>$request->email,'guard'=>constGuards::ADMIN])
                  ->first();

                  if( $oldToken){
                    DB::table('password_reset_tokens')
                        ->where(['email'=>$request->email,'guard'=>constGuards::ADMIN])
                        ->update([
                          'token'=>$token,
                          'created_at'=>Carbon::now()
                        ]);
                  }else{
                    DB::table('password_reset_tokens')->insert([
                      'email'=>$request->email,
                      'guard'=>constGuards::ADMIN,
                      'token'=>$token,
                      'created_at'=>Carbon::now()
                    ]);
                  } 
                
                 $actionLink = route('admin.reset-password',['token'=>$token,'email'=>$request->email]);
                 
                 $data = array(
                  'actionLink'=>$actionLink,
                  'admin'=>$admin
                 );

                 $mail_body = view('email-templates.admin-forgot-email-template',$data)->render();
                 
                 $mailConfig = array(
                  'mail_from_email'=>env('EMAIL_FROM_ADDRESS'),
                  'mail_from_name'=>env('EMAIL_FROM_NAME'),
                  'mail_recipient_email'=>$admin->email,
                  'mail_recipient_name'=>$admin->name,
                  'mail_subject'=>'Reset password',
                  'mail_body'=>$mail_body
                 );
                 
                 if ( sendEmail($mailConfig)){
                  session()->flash('success','We have e-mailed your password reset link.');
                  return redirect()->route('admin.forgot-password');

                 }else{
                  session()->flash('fail','Something wend wrongg!');
                  return redirect()->route('admin.forgot-password');
                 }

   }
   public function profileView (Request $request){
    $admin = null;
    if( Auth::guard('admin')->check()){
      $admin = Admin::findOrFail(auth()->id());
    }
    return view('back.pages.admin.profile',compact('admin'));
   }

   public function updatePassword(Request $request)
{
    $request->validate([
        'old_password' => 'required',
        'new_password' => 'required|min:8',
    ]);

    $admin = Auth::guard('admin')->user();

    if (!Hash::check($request->old_password, $admin->password)) {
      return redirect()->back()->with('error', 'The old password does not match.');
  }

    // Update the admin's password
    $admin->password = Hash::make($request->new_password);
    $admin->save();

    return redirect()->route('admin.profile')->with('success', 'Password updated successfully.');
    }


}
