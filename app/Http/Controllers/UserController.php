<?php

namespace App\Http\Controllers;

use App\Models\Auth\Role\Role;
use App\Models\Auth\User\User;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseControllers;
use Validator;
use Auth;
use Hash;

class UserController extends BaseControllers
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    private function admin_credential_rules(array $data)
    {
      $messages = [
        'current-password.required' => 'Please enter current password',
        'password.required' => 'Please enter password',
      ];
    
      $validator = Validator::make($data, [
        'current-password' => 'required',
        'password' => 'required|same:password',
        'password_confirmation' => 'required|same:password',     
      ], $messages);
    
      return $validator;
    }

    public function postCredentials(Request $request)
    {
      if(Auth::Check())
      {
        $request_data = $request->All();
        $validator = $this->admin_credential_rules($request_data);
        if ($validator->fails()) return redirect()->back()->withErrors($validator->errors());
        else
        {  
          $current_password = Auth::User()->password;           
          if(Hash::check($request_data['current-password'], $current_password))
          {           
            $user_id = Auth::User()->id;                       
            $obj_user = User::find($user_id);
            $obj_user->password = Hash::make($request_data['password']);;
            $obj_user->save();
            return \Redirect::back()->withSuccess( 'Password changes successfully' );
          }
          else
          {           
            $error = array('current-password' => 'Please enter correct current password');
            return redirect()->back()->withErrors($error);  
          }
        }        
      }
      else
      {
        return redirect()->to('/');
      }    
    }

    public function changePassword()
    {
      if (Auth::user() &&  Auth::user()->hasRole('administrator'))
          return view('user.adminchangepassword');
      elseif(Auth::user() &&  Auth::user()->hasRole('staff'))
          return view('user.staffchangepassword');
      elseif(Auth::user() &&  Auth::user()->hasRole('student'))
          return view('user.studentchangepassword');
    }

    public function profile()
    {
        $user = Auth::user();
        if(Auth::user() &&  Auth::user()->hasRole('staff'))
            return view('user.staffprofile', compact('user'));
        elseif(Auth::user() &&  Auth::user()->hasRole('student'))
            return view('user.studentprofile', compact('user'));
    }
}
