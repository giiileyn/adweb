<?php

namespace App\Http\Controllers\Seller;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;

class sellerController extends Controller
{
    use \Illuminate\Foundation\Validation\ValidatesRequests;

    // public function show ()
    // {
    //     return view('back.pages.seller.auth.register');
    // }

    public function show(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'picture' => 'required|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $user = Auth::user();

        $seller = new Seller();
        $seller->user_id = $user->id;
        $seller->name = $validatedData['name'];
        $seller->address = $validatedData['address'];
        $seller->phone = $validatedData['phone'];
        $seller->email = $user->email; // Use logged-in user's email
        $seller->password = $user->password; // Use logged-in user's password (hashed)

        if ($request->hasFile('uploads')) {
            $file = $request->file('uploads');
            $filePath = 'public/back/images/sellers/' . $file->getClientOriginalName();
            Storage::put($filePath, file_get_contents($file));
            $seller->image_path = $filePath;
        }

        $seller->save();

        return response()->json([
            "success" => "Seller created successfully.",
            "seller" => $seller,
            "status" => 200
        ]);
    }


    // public function store(Request $request)
    // {
    //     $validatedData = $request->validate([
    //         'name' => 'required|string|max:255',
    //         'address' => 'required|string|max:255',
    //         'phone' => 'required|string|max:15',
    //         'uploads' => 'required|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
    //     ]);

    //     $user = new User([
    //     'name' => $validatedData['name'],
    //     'email' => $validatedData['email'],
    //     'password' => bcrypt($validatedData['password']),
    //     ]);

    //     $user->save();

    //     $seller = new Seller();
    //         $seller -> user_id = $user ->id;
    //         $seller ->name = $request->name;
    //         $seller ->address = $request->address;
    //         $seller ->phone = $request->phone;
    //         $files = $request->file('sellers');
    //         $seller->image_path = 'storage/images/' . $files->getClientOriginalName();
    //         $seller->save();

    //         // 'user_id' => $user->id,
    //         // 'name' => $validatedData['name'],
    //         // 'address' => $validatedData['address'],
    //         // 'phone' => $validatedData['phone'],
    //         // 'picture' => 'storage/images/' . $request->file('uploads')->getClientOriginalName(),
    
    //     Storage::put(
    //         'public/back/images/users/' . $request->file('sellers')->getClientOriginalName(),
    //         file_get_contents($request->file('sellers'))
    //     );
    
    //     return response()->json([
    //         "success" => "customer created successfully.",
    //         "seller" => $seller,
    //         "status" => 200
    //     ]);
    // }



    
    //-------------------------------------------------------------------------------------------------------------
    // public function register()
    // {
    //     return view('back.pages.seller.auth.register');
    // }


    // public function postSignup(Request $request){
    //     $this->validate($request, [
    //         'name' => 'required|string|max:255',
    //         'username' => 'required|string|max:255|unique:seller,username',
    //         'address' => 'required|string|max:255',
    //         'phone' => 'required|string|max:15',
    //         'email' => 'required|string|email|max:255|unique:seller,email',
    //         'password' => 'required|string|min:8|confirmed',
    //     ],[
    //         'email.required' => 'email required'
    //     ]);

    //     dd($request->all());

    //     $seller = new Seller();
    //     $seller->name = $request->input('name');
    //     $seller->username = $request->input('username');
    //     $seller->address = $request->input('address');
    //     $seller->phone = $request->input('phone');
    //     $seller->email = $request->input('email');
    //     $seller->password = Hash::make($request->input('password'));

    //     try {
    //         $seller->save();
    //         Log::info('Data saved successfully.');
    //     } catch (QueryException $e) {
    //         // Log the error message
    //         Log::error('Error saving data: ', ['error' => $e->getMessage()]);
    //         return redirect()->route('seller.register')->withErrors('Registration failed. Please try again.');
    //     }
    //     return redirect()->route('seller.register')->with('success', 'Registration successful!');

    // }

//----------------------------------------------------------------------------------------------------
    // public function login(Request $request){
    //     $data = [
    //         'pageTitle'=>'Seller Login'
    //     ];
    //     return view ('back.pages.seller.auth.login',$data);
    // }

    // public function register(Request $request){
    //     $data = [
    //         'pageTitle'=>'Seller Register Account'
    //     ];
    //     return view ('back.pages.seller.auth.register',$data); 
    // }

    // public function home(Request $request){
    //     $data = [
    //         'pageTitle'=>'Seller Dashboard'
    //     ]; 
    //     return view ('back.pages.seller.home',$data); 
    // }


   
    // public function register(Request $request){
    //     return view('back.pages.seller.auth.register');
    //     $validateSeller = Validator::make($request->all(),
    //     [
    //         'name'=> 'required',
    //         'email'=>'required|email|unique:seller,email',
    //         'password'=> 'required',
    //     ]);
    //     if($validateSeller->fails()){
    //         return response()->json([
    //             'status'=> false,
    //             'message'=>'validation error',
    //             'errors' => $validateSeller->errors()
    //         ]);
    //     }
    // }

    // public function postSignup(){

    //     $request->validate([
    //        'name' => 'required|string|max:255',
    //         'username' => 'required|string|max:255|unique:sellers,username',
    //         'address' => 'required|string|max:255',
    //         'phone' => 'required|string|max:15',
    //         'email' => 'required|string|email|max:255|unique:sellers,email',
    //         'password' => 'required|string|min:8|confirmed', 
            
    //     ]
    //     //,[
    //         // 'name.required'=>'Must enter ur name',
    //         // 'name.string'=>'name must be string char',
    //    // ]
    //     );
    //     $seller = new Seller();
    //     $seller->name = $request->input('name');
    //     $seller->username = $request->input('username');
    //     $seller->address = $request->input('address');
    //     $seller->phone = $request->input('phone');
    //     $seller->email = $request->input('email');
    //     $seller->password = bcrypt($request->input('password'));
    //     $seller->save();

    //     return redirect()->route('seller.register')->with('success', 'Registration successful!');
    // }
}
