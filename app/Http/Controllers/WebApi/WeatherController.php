<?php

namespace App\Http\Controllers\WebApi;

use App\Http\Controllers\Controller;
use App\User;
use DmitryIvanov\DarkSkyApi\DarkSkyApi;
use Illuminate\Http\Request;
use Spatie\Geocoder\Exceptions\CouldNotGeocode;
use Spatie\Geocoder\Geocoder;

class WeatherController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * WeatherController::getWeather()
     *
     * @param Request $request
     * @param int $id
     * @param string $location
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getWeather (Request $request, int $id, string $location = null)
    {
        // Get user
        $user = User::findOrFail($id);

        $locationValues = [
            "name"  =>      null,
            "latitude" =>   null,
            "longitude" =>  null,
        ];

        if (!isset($location) || empty($location)) {
            $locationValues['name'] = $user->location_name;
            $locationValues['latitude'] = $user->location_latitude;
            $locationValues['longitude'] = $user->location_longitude;
        } else {
            $locationValues['name'] = $location;

            try {
                $client = new \GuzzleHttp\Client();
                $geocoder = new Geocoder($client);
                $geocoder->setApiKey(config('geocoder.key'));
                $details = $geocoder->getCoordinatesForAddress($locationValues['name']);

                $locationValues['latitude'] = $details['lat'];
                $locationValues['longitude'] = $details['lng'];
            } catch (CouldNotGeocode $e) {
                // Die silently inside
            }
        }

        // Work out 'current date'
        $days = [
            'current' => date('Y-m-d'),
            'monday' => date('Y-m-d', strtotime( 'next monday')),
            'tuesday' => date('Y-m-d', strtotime( 'next tuesday')),
            'wednesday' => date('Y-m-d', strtotime( 'next wednesday')),
            'thursday' => date('Y-m-d', strtotime( 'next thursday')),
            'friday' => date('Y-m-d', strtotime( 'next friday')),
            'saturday' => date('Y-m-d', strtotime( 'next saturday')),
            'sunday' => date('Y-m-d', strtotime( 'next sunday')),
        ];

        $timeMachine = null;
        if (!empty($locationValues['latitude']) && !empty($locationValues['longitude'])) {
            try {
                $timeMachine = (new DarkSkyApi($_ENV['DARK_SKY_API_KEY']))
                    ->location($locationValues['latitude'], $locationValues['longitude'])
                    ->timeMachine($days);
            } catch (\Throwable $e) {
                $timeMachine = false;
            }
        }

        $returnData = [
            "location"      => $locationValues['name'],
        ];

        if (!empty($timeMachine)) {
            foreach ($days as $key => $day) {
                $returnData[$key] = [
                    "date"  => $day,
                    "weather" => $timeMachine[$day]->daily()->summary(),

                ];
            }
        }
        return response()->json($returnData);
    }
}
