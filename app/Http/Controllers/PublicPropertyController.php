<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;



class PublicPropertyController extends Controller
{
    public function show($slug)
    {
        $property = Property::where('slug', $slug)->firstOrFail();

        // generar enlace firmado válido por 15 días
        $signedUrl = URL::temporarySignedRoute(
            'property.signed.show',
            now()->addDays(15),
            ['property' => $property->id]
        );


        return view('properties.show', compact('property', 'signedUrl') );
    }
    public function showById($id)
    {
        $property = Property::where('id', $id)->firstOrFail();


        // generar enlace firmado válido por 15 días
        $signedUrl = URL::temporarySignedRoute(
            'property.signed.show',
            now()->addDays(15),
            ['property' => $property->id]
        );


        return view('properties.show', compact('property', 'signedUrl') );
    }


    public function showSigned(Property $property)
    {
        return view('properties.show', compact('property'));
    }
}
