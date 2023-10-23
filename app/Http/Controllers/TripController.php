<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use Illuminate\Http\Request;

class TripController extends Controller
{    
    /**
     * store
     *
     * @param Request $request
     * @return Request $request
     */
    public function store(Request $request)
    {
        $request->validate([
            'origin' => 'required',
            'destination' => 'required',
            'destination_name' => 'required',
        ]);
        return $request->user()->trips()->create(
            $request->only([
                'origin',
                'destination',
                'destination_name'
            ])
        );
    }
    
    /**
     * show a trip
     *
     * @param Request $request
     * @param Trip $trip
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, Trip $trip)
    {
        if ($trip->user->id === $request->user()->id) {
            return $trip;
        }
        if ($trip->driver && $request->user()->driver) {
            if ($trip->driver->id === $request->user()->driver->id) {
                return $trip;
            }
        }
        return response()->json(['message' => 'Cannnot find this trip'], 404);
    }

        
    /**
     * a driver accepts a trip
     *
     * @param  mixed $request
     * @param  mixed $trip
     * @return void
     */
    public function accept(Request $request, Trip $trip)
    {
        $request->validate([
            'driver_location' => 'required'
        ]);
        $trip->update([
            'driver_id' => $request->user()->id,
            'driver_location' => $request->driver_location
        ]);
        $trip->load('driver.user');
        return $trip;

    }
    
    /**
     * a driver has stared taking a passenger to their destination
     *
     * @param Request $request
     * @param Trip $trip
     * @return Trip $trip
     */
    public function start(Request $request, Trip $trip)
    {
        $trip->update([
            'is_started' => true
        ]);
        $trip->load('driver.user');
        return $trip;

    }
        
    /**
     * a driver has ended a trip
     *
     * @param Trip $trip
     * @return Trip $trip
     */
    public function end(Trip $trip)
    {
        $trip->update([
            'is_complete' => true
        ]);
        $trip->load('driver.user');
        return $trip;
    }
    
    /**
     * update the driver's current location
     *
     * @param Request $request
     * @param Trip $trip
     * @return Trip $trip
     */
    public function location(Request $request, Trip $trip)
    {
        $request->validate([
            'driver_location' => 'required'
        ]);
        $trip->update([
            'driver_location' => $request->driver_location
        ]);
        $trip->load('driver.user');
        return $trip;
    }
   
}
