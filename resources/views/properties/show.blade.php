@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4">{{ $property->property_title }}</h1>

        @if (auth()->check() && isset($signedUrl))
            <div class="mb-4">
                <a href="{{ $signedUrl }}" target="_blank" class="btn btn-primary">
                    Ver enlace temporal (15 días)
                </a>
                <p class="text-sm text-muted mt-2">{{ $signedUrl }}</p>
            </div>
        @endif

        {{-- Datos principales --}}
        <div class="row mb-4">
            <div class="col-md-8">
                <p><strong>Status:</strong> {{ $property->status->status_name ?? 'N/A' }}</p>
                <p><strong>Type:</strong> {{ $property->type->type_name ?? 'N/A' }}</p>
                <p><strong>Location:</strong> {{ $property->location->full_path ?? 'N/A' }}</p>
                <p><strong>Added Date:</strong> {{ $property->property_added_date }}</p>

                <p><strong>Bedrooms:</strong> {{ $property->property_bedrooms }}</p>
                <p><strong>Bathrooms:</strong> {{ $property->property_bathrooms }}</p>
                <p><strong>Bathrooms (Inner):</strong> {{ $property->property_bathrooms_inner }}</p>
                <p><strong>Size:</strong> {{ $property->property_building_size_area_quantity }} {{ $property->property_building_size_area_unit }}</p>
                <p><strong>Lot Size:</strong> {{ $property->property_lot_size_area_quantity }} {{ $property->property_lot_size_area_unit }}</p>

                <p><strong>Price:</strong> ${{ number_format($property->property_price, 2) }}</p>
                <p><strong>HOA Fee:</strong> ${{ number_format($property->property_hoa_fee, 2) }}</p>

                <p><strong>Floor No:</strong> {{ $property->property_on_floor_no }}</p>
                <p><strong>No. of Floors:</strong> {{ $property->property_no_of_floors }}</p>

                <p><strong>Video:</strong>
                    @if ($property->property_video)
                        <a href="{{ $property->property_video }}" target="_blank">Ver Video</a>
                    @else
                        N/A
                    @endif
                </p>

                <p><strong>Description:</strong></p>
                <div>{!! nl2br(e($property->property_body)) !!}</div>

                {{-- Características --}}
                <p class="mt-4"><strong>Features:</strong></p>
                @if ($property->features && $property->features->count())
                    <ul>
                        @foreach ($property->features as $feature)
                            <li>{{ $feature->feature_name }}</li>
                        @endforeach
                    </ul>
                @else
                    <p>No hay características registradas.</p>
                @endif

                {{-- Referencias de venta --}}
                <h5 class="mt-4">Sold References</h5>
                @if ($property->soldReferences && $property->soldReferences->count())
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>Date</th>
                            <th>Price</th>
                            <th>Notes</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($property->soldReferences as $ref)
                            <tr>
                                <td>{{ $ref->sold_reference_date }}</td>
                                <td>${{ number_format($ref->sold_reference_price, 2) }}</td>
                                <td>{{ $ref->sold_reference_notes }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <p>No hay referencias de venta.</p>
                @endif

                {{-- Listing Competitors --}}
                <h5 class="mt-4">Listing Competitors</h5>
                @if ($property->listingCompetitors && $property->listingCompetitors->count())
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>Listing Agent</th>
                            <th>Company</th>
                            <th>Link</th>
                            <th>Price</th>
                            <th>Notes</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($property->listingCompetitors as $comp)
                            <tr>
                                <td>{{ $comp->competitor_listing_agent }}</td>
                                <td>{{ $comp->company->company_name ?? 'N/A' }}</td>
                                <td>
                                    @if ($comp->competitor_property_link)
                                        <a href="{{ $comp->competitor_property_link }}" target="_blank">Ver</a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>${{ number_format($comp->competitor_list_price, 2) }}</td>
                                <td>{{ $comp->competitor_notes }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <p>No hay propiedades en competencia listadas.</p>
                @endif
            </div>

            {{-- Fotos --}}
            <div class="col-md-4">
                <h4>Fotos</h4>
                @if ($property->photos && $property->photos->count())
                    @foreach ($property->photos as $photo)
                        <img src="{{ asset($photo->photo_url) }}" alt="{{ $photo->photo_alt }}" class="img-fluid mb-2">
                    @endforeach
                @else
                    <p>No hay fotos disponibles.</p>
                @endif
            </div>
        </div>
    </div>
@endsection
