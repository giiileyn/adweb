<?php

namespace App\Http\Controllers\account;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class accountController extends Controller
{
    public function register()
    {
        return view('back.pages.account.auth.register');
    }

    public function postSignup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'email|required|unique:users',
            'password' => 'required|min:8'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);
        $user->save();
    
        Auth::login($user);
        return redirect()->route('back.pages.account.auth.register')->with('success', 'You are registered.');
    }

    public function login()
    {
        return view('back.pages.account.auth.login');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }

    public function postSignin(Request $request)
    {
        $this->validate($request, [
            'email' => 'email| required',
            'password' => 'required| min:4'
        ]);

        if (auth()->attempt(array('email' => $request->email, 'password' => $request->password))) {

            return redirect('/');
        } else {
            return redirect()->route('user.login')
                ->with('error', 'Email-Address And Password Are Wrong.');
        }
    }

      // public function store(Request $request)
    // {
    //     // Define validation rules
    //     $validator = Validator::make($request->all(), [
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|string|email|max:255|unique:users',
    //         'password' => 'required|string|min:8',
    //     ]);

    //     // If validation fails, return JSON response with errors
    //     if ($validator->fails()) {
    //         return response()->json(['error' => $validator->errors()], 422);
    //     }

    //     // Create new user
    //     $user = User::create([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'password' => bcrypt($request->password),
    //     ]);

    //     // Return JSON response with created user and status code
    //     return response()->json(['user' => $user], 201);
    // }

    // /**
    //  * Retrieve the specified user.
    //  *
    //  * @param  \App\Models\User  $user
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // public function show(User $user)
    // {
    //     return response()->json(['user' => $user]);
    // }

    // /**
    //  * Update the specified user in storage.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @param  \App\Models\User  $user
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // public function update(Request $request, User $user)
    // {
    //     $user->update([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'password' => bcrypt($request->password),
    //     ]);

    //     return response()->json(['user' => $user]);
    // }

    // /**
    //  * Remove the specified user from storage.
    //  *
    //  * @param  \App\Models\User  $user
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // public function destroy(User $user)
    // {
    //     $user->delete();

    //     return response()->json(['message' => 'User deleted successfully']);
    // }
   
}
