<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class RestaurantController extends Controller
{
    public function index()
    {
        $googleMapsApiKey = env('GOOGLE_MAPS_API_KEY');
        return view('restaurants', compact('googleMapsApiKey'));
    }

    public function search($keyword = 'Bang Sue')
    {
        $cacheKey = 'restaurants_search_' . $keyword;
        $results = Cache::remember($cacheKey, 60, function () use ($keyword) {
            $apiKey = env('GOOGLE_MAPS_API_KEY');
            $response = Http::get("https://maps.googleapis.com/maps/api/place/textsearch/json?query=restaurants+in+$keyword&type=restaurant&key=$apiKey");
            return $response->json();
        });

        return response()->json($results);
    }
}

