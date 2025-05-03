<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Location;


class LocationController extends Controller
{
    public function getLocations()
    {
        $locations = Location::all(); // Mengambil semua data lokasi
        return response()->json($locations);
    }

}
