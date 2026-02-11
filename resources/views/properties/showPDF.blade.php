<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $property->property_title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
            margin: 10px;
        }
        h1 {
            font-size: 24px;
            margin-bottom: 10px;
        }
        h3 {
            font-size: 18px;
            margin-top: 5px;
            margin-bottom: 3px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 2px;
        }
        .info-label {
            font-weight: bold;
            color: #555;
        }
        .section {
            margin-bottom: 2px;
        }
        ul {
            padding-left: 20px;
            margin-top: 5px;
        }
        li {
            margin-bottom: 4px;
        }
        table {
            width: 100%;
            border: none !important;
            margin-top: 2px;
        }
        th, td {
            border: none !important;
            padding:2px;
            font-size: 11px;
        }
        th {
            background-color: #eee;
        }

        .thumbnails img {
            max-width: 80px;
            max-height: 60px;
            margin: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
            display: inline-block;
        }

        .photos img {
            max-width: 200px;
            height: auto;
            margin: 10px 10px 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            display: inline-block;
            vertical-align: top;
        }


        ul._features {
            margin: 0;
            padding: 0;
            list-style: none; /* quítalo si quieres viñetas */
            font-size: 12px;
        }

        ul._features  > li{
            display: inline-block;
            width: 30%;          /* 100 / 5 */
            vertical-align: top;
            box-sizing: border-box;
            padding: 2px 6px;
            margin: 0;
        }


    </style>
</head>
<body>

<div class="section" style="background-color: #ccc; text-align: center">
    <img src="{{ public_path('images/remax-header-pdf.png') }}" style="width: 40%;">
</div>

<h3>{{ $property->property_title }}</h3>

{{-- Description --}}
<div class="section">
    <div>{!! $property->property_body !!}</div>
</div>

<div class="section">
    <h3>Details</h3>
    <div>

        <table style="border: none;" border="0" cellpadding="0" >
            <tr>
                <td width="33%">
                    <p><span class="info-label">Property ID:</span> {{ $property->id }}</p>
                    <p><span class="info-label">Property Type:</span> {{ $property->type?->type_name }}</p>
                    <p><span class="info-label">Bedrooms:</span> {{ $property->property_bedrooms }}</p>
                    <p><span class="info-label">Bathrooms:</span> {{ $property->property_bathrooms }}</p>

                </td>

                <td width="33%">

                    <p><span class="info-label">Building Size (m2):</span> {{ $property->property_building_size_area_quantity }} {{ $property->property_building_size_area_unit }}</p>
                    <p><span class="info-label">Lot Size (m2):</span> {{ $property->property_lot_size_area_quantity }} {{ $property->property_lot_size_area_unit }}</p>
                    <p><span class="info-label">On Floor No.:</span> {{ $property->property_on_floor_no }}</p>
                    <p><span class="info-label">No. of Floors:</span> {{ $property->property_no_of_floors }}</p>

                </td>
                <td width="33%">

                    <p><span class="info-label">Price:</span> ${{ number_format($property->property_price, 2) }}</p>
                    <p><span class="info-label">Status:</span> {{ $property->status?->status_name }}</p>
                    <p><span class="info-label">Location:</span> {{ $property->location?->full_path ?? $property->location?->location_name }}</p>
                    <p><span class="info-label">Monthly HOA Fee: $</span> {{ $property->property_hoa_fee }}</p>

                </td>

            </tr>

        </table>

    </div>
</div>

<div class="section">
    <h3>Features</h3>
    <div>

        @if ($property->features->count())


            <ul class="_features">
                @foreach ($property->features as $feature)
                    <li>{{ $feature->feature_name }}</li>
                @endforeach
            </ul>

        @endif

    </div>
</div>






{{-- Sold References --}}
@if ($property->soldReferences->count())
<!--     <div class="section">
        <h3>Sold References</h3>
        <table>
            <thead>
            <tr>
                <th>Date</th>
                <th>Price</th>
                <th>Notes</th>
            </tr>
            </thead>
            <tbody>
            @foreach($property->soldReferences as $ref)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($ref->sold_reference_date)->format('M d, Y') }}</td>
                    <td>${{ number_format($ref->sold_reference_price, 2) }}</td>
                    <td>{!! $ref->sold_reference_notes !!}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>-->
@endif

{{-- Listing Competitors --}}
@if ($property->listingCompetitors->count())
    <!--    <div class="section">
        <h3>Listing Competitors</h3>
        <table>
            <thead>
            <tr>
                <th>Company</th>
                <th>Link</th>
                <th>Price</th>
            </tr>
            </thead>
            <tbody>
            @foreach($property->listingCompetitors as $comp)
                <tr>
                    <td>{{ $comp->competitor_company_name }}</td>

                    <td>
                        @if($comp->competitor_property_link)
                            <a href="{{ $comp->competitor_property_link }}" target="_blank">{{ \Illuminate\Support\Str::limit($comp->competitor_property_link, 30) }}</a>
                        @else
                            —
                        @endif
                    </td>
                    <td>${{ number_format($comp->competitor_list_price, 2) }}</td>

                </tr>
            @endforeach
            </tbody>
        </table>
    </div> -->
@endif

{{-- Notes to Agents --}}
@if ($property->property_notes_to_agents)
    {{--    <div class="section">
            <h3>Notes To Agents</h3>
            <div>{!! $property->property_notes_to_agents !!}</div>
        </div> --}}
    @endif

    {{-- Photos as thumbnails at the end --}}
@php
    $photos = $property->getMedia('gallery');
@endphp

@if ($photos->count())
    <div class="section photos">
        <h3>Photos</h3>
        @foreach($photos as $media)
            <img src="{{ $media->getPath() }}" alt="Photo">
        @endforeach
    </div>
@endif

</body>
</html>
