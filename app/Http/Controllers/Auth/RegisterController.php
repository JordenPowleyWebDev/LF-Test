<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use http\Exception;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Geocoder\Exceptions\CouldNotGeocode;
use Spatie\Geocoder\Geocoder;

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
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'      => ['required', 'string', 'min:8', 'confirmed'],
            'location_name' => ['required', 'string', 'min:2'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        // Get the coords from the input location
        try {
            $client = new \GuzzleHttp\Client();
            $geocoder = new Geocoder($client);
            $geocoder->setApiKey(config('geocoder.key'));
//            $geocoder->setCountry(config('UK'));
            $details = $geocoder->getCoordinatesForAddress($data['location_name']);

            $data['location_longitude'] = $details['lng'];
            $data['location_latitude'] = $details['lat'];

        } catch (CouldNotGeocode $e) {
            $data['location_longitude'] = null;
            $data['location_latitude'] = null;
        } catch(Exception $e) {
            $data['location_longitude'] = null;
            $data['location_latitude'] = null;
        }

        return User::create([
            'name'                  => $data['name'],
            'email'                 => $data['email'],
            'location_name'         => $data['location_name'],
            'location_longitude'    => $data['location_longitude'],
            'location_latitude'     => $data['location_latitude'],
            'password'              => Hash::make($data['password']),
        ]);
    }
}
