<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Mail\userRegistered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\mail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
	 
	public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));


        return redirect('login')->with('warning',' Silahkan Periksa Email Anda');
    } 
	 
	 
    protected function create(array $data)
    {
        $user =  User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
			'token' =>str_random(20),
        ]);
		
		//mengirim email
		Mail::to($user->email)->send(new userRegistered($user));
    }
	
	public function verify_register($token, $id)
	{
		$user = User::find($id);
		
		if(!$user)
		{
			return redirect('login')->with('warning','user tak ada');
		}
		
		if($user->token != $token)
		{
			return redirect('login')->with('warning','Tokennya salah sayang');
		}
		
		$user->status = 1;
		$user->save();
		
		$this->guard()->login($user);
		return redirect('home');
	}
}
