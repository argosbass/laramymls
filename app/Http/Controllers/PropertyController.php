<?php


namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function show($id)
    {

        $property = Property::with([
            'status',
            'type',
            'location.parent',
            'photos',
            'features',
            'soldReferences',
            'listingCompetitors.company',
        ])->findOrFail($id);

        return view('properties.show', compact('property'));

        //$property = Property::with(['status', 'type', 'features', 'location', 'location.parent', 'photos'])->findOrFail($id);
        //return response()->json($property);
    }



    public function index()
    {
        $properties = Property::with(['status', 'type', 'features', 'location', 'location.parent','photos'])->get();
        return response()->json($properties);
    }
}
