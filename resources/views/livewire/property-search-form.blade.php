@php
    use Carbon\Carbon;
@endphp


<div class="space-y-6 w-full">
    <form wire:submit.prevent="search">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="title">Title</label>
                <input id="title" type="text" wire:model.defer="title"
                       class="block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-primary-300 focus:border-primary-300"/>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="propertyId">Property ID</label>
                <input id="propertyId" type="number" wire:model.defer="propertyId"
                       class="block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-primary-300 focus:border-primary-300"/>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="typeId">Type</label>
                <select id="typeId" wire:model="typeId"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-primary-300 focus:border-primary-300">
                    <option value="">All</option>
                    @foreach ($types as $type)
                        <option value="{{ $type->id }}">{{ $type->type_name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="statusId">Status</label>
                <select id="statusId" wire:model="statusId"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-primary-300 focus:border-primary-300">
                    <option value="">All</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->id }}">{{ $status->status_name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="locationId">Location</label>
                <select id="locationId" wire:model="locationId"
                        class="block w-full rounded-md border border-gray-300 shadow-sm focus:ring focus:ring-primary-300 focus:border-primary-300">
                    <option value="">All</option>
                    @foreach ($locations as $location)
                        <option value="{{ $location->id }}" style="{{ $location->depth == 1 ? 'font-weight: 700;' : '' }}">
                            {!! str_repeat(' - ', $location->depth) !!} {{ $location->location_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="year">Year</label>
                <select id="year" wire:model="year"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-primary-300 focus:border-primary-300">
                    <option value="">All</option>
                    @foreach ($years as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <div class="flex gap-4 w-full">
                    <div class="flex flex-col flex-1 min-w-0">
                        <label for="priceFrom" class="block text-sm font-medium text-gray-700 mb-1">Price From</label>
                        <input id="priceFrom" type="number" wire:model.defer="priceFrom"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-primary-300 focus:border-primary-300"/>
                    </div>

                    <div class="flex flex-col flex-1 min-w-0">
                        <label for="priceTo" class="block text-sm font-medium text-gray-700 mb-1">Price To</label>
                        <input id="priceTo" type="number" wire:model.defer="priceTo"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-primary-300 focus:border-primary-300"/>
                    </div>
                </div>
            </div>


            <div>
                <div class="flex gap-4 w-full">
                    <div class="flex flex-col flex-1 min-w-0">
                        <label class="block text-sm font-medium text-gray-700 mb-1" for="bedroomsFrom">Bedrooms
                            From</label>
                        <input id="bedroomsFrom" type="number" wire:model.defer="bedroomsFrom"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-primary-300 focus:border-primary-300"/>
                    </div>

                    <div class="flex flex-col flex-1 min-w-0">
                        <label class="block text-sm font-medium text-gray-700 mb-1" for="bedroomsTo">Bedrooms To</label>
                        <input id="bedroomsTo" type="number" wire:model.defer="bedroomsTo"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-primary-300 focus:border-primary-300"/>

                    </div>
                </div>
            </div>

            <div>
                <div class="flex gap-4 w-full">
                    <div class="flex flex-col flex-1 min-w-0">
                        <label class="block text-sm font-medium text-gray-700 mb-1" for="bathroomsFrom">Bathrooms
                            From</label>
                        <input id="bathroomsFrom" type="number" wire:model.defer="bathroomsFrom"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-primary-300 focus:border-primary-300"/>

                    </div>

                    <div class="flex flex-col flex-1 min-w-0">
                        <label class="block text-sm font-medium text-gray-700 mb-1" for="bathroomsTo">Bathrooms
                            To</label>
                        <input id="bathroomsTo" type="number" wire:model.defer="bathroomsTo"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-primary-300 focus:border-primary-300"/>

                    </div>
                </div>
            </div>

            <div>
                <div class="flex gap-4 w-full">
                    <div class="flex flex-col flex-1 min-w-0">
                        <label class="block text-sm font-medium text-gray-700 mb-1" for="buildingFrom">Building Size
                            From (m²)</label>
                        <input id="buildingFrom" type="number" wire:model.defer="buildingFrom"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-primary-300 focus:border-primary-300"/>

                    </div>

                    <div class="flex flex-col flex-1 min-w-0">
                        <label class="block text-sm font-medium text-gray-700 mb-1" for="buildingTo">Building Size To
                            (m²)</label>
                        <input id="buildingTo" type="number" wire:model.defer="buildingTo"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-primary-300 focus:border-primary-300"/>

                    </div>
                </div>
            </div>

            <div>
                <div class="flex gap-4 w-full">
                    <div class="flex flex-col flex-1 min-w-0">
                        <label class="block text-sm font-medium text-gray-700 mb-1" for="lotFrom">Lot Size From
                            (m²)</label>
                        <input id="lotFrom" type="number" wire:model.defer="lotFrom"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-primary-300 focus:border-primary-300"/>

                    </div>

                    <div class="flex flex-col flex-1 min-w-0">
                        <label class="block text-sm font-medium text-gray-700 mb-1" for="lotTo">Lot Size To (m²)</label>
                        <input id="lotTo" type="number" wire:model.defer="lotTo"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-primary-300 focus:border-primary-300"/>

                    </div>
                </div>
            </div>

        </div>

        @php
            $features_label = [
                        'Condominium Community' => 'CC',
                        'Oceanfront'            => 'OF',
                        'Elevator'              => 'EL',
                        'Gated Community'       => 'GC',
                        'Golf Front'            => 'GF',
                        'Ocean Views'           => 'OV',
                        'Swimming Pool'         => 'SP',
                        'Owner Financing'       => 'OF',
                        'Sold Furnished'        => 'SF',
                        'Guest House'           => 'GH',

                ];
        @endphp

        <div class="mt-4">
            <label class="block font-medium text-sm text-gray-700">Features</label>
            <div class="flex flex-wrap gap-4 mt-2">
                @foreach ($featuresList as $feature)
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" wire:model="features" value="{{ $feature->id }}"/>&nbsp;
                        <span>{{ $feature->feature_name }} <b>({{$features_label[ $feature->feature_name ]}})</b></span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="mt-6">
            <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-white hover:bg-primary-700 focus:outline-none focus:ring focus:ring-primary-300 active:bg-primary-900 transition">
                Search
            </button>
        </div>
    </form>

    {{-- Tabla de resultados --}}
    @if ($results && $results->count())
        <div class="overflow-x-auto mt-6">

            <div class="mt-4 p-4">
                {{ $results->links() }}
            </div>



            <table class="w-full table-auto text-sm text-left border border-gray-300">
                <thead>
                <tr class="bg-gray-100 text-xs uppercase">
                    <th class="px-2 py-1">ID</th>
                    <th class="px-2 py-1">Date Added</th>
                    <th class="px-2 py-1">Date Sold</th>
                    <th class="px-2 py-1">Type</th>
                    <th class="px-2 py-1">Status</th>
                    <th class="px-2 py-1">Title</th>
                    <th class="px-2 py-1">Location</th>
                    <th class="px-2 py-1">Price</th>
                    <th class="px-2 py-1">Beds</th>
                    <th class="px-2 py-1">Baths</th>
                    <th class="px-2 py-1">HOA</th>
                    <th class="px-2 py-1">Building (m²)</th>
                    <th class="px-2 py-1">Lot (m²)</th>
                    <th class="px-2 py-1">Floors</th>
                    @foreach ($featuresList as $feature)
                        <th class="px-2 py-1 text-center"> {{$features_label[ $feature->feature_name ]  }}</th>
                    @endforeach
                    <th class="px-2 py-1">Print PDF</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($results as $property)
                    <tr class="border-t">
                        <td class="px-2 py-1">{{ $property->id }}</td>
                        <td class="px-2 py-1">{{ $property->created_at->format('Y-m-d') }}</td>

                        @php
                            $sold = $property->sold_at;
                        @endphp

                        <td class="px-2 py-1">
                            {{ $sold ? \Carbon\Carbon::parse($sold->sold_reference_date)->format('Y-m-d') : '-' }}
                        </td>
                        <td class="px-2 py-1">{{ $property->type->type_name ?? '-' }}</td>
                        <td class="px-2 py-1">{{ $property->status->status_name ?? '-' }}</td>

                        <td class="px-2 py-1">

                            <a href="{{ $property->slug
            ? route('property.public.show',     ['slug' => $property->slug])
            : route('property.public.showById', ['id' => $property->id]) }}"
                               class="text-primary-600 hover:underline font-semibold">
                                {{ $property->property_title }}
                            </a>


                        </td>

                        <td class="px-2 py-1">{{ $property->location->location_name ?? '-' }}</td>
                        <td class="px-2 py-1">${{ number_format($property->property_price) }}</td>
                        <td class="px-2 py-1">{{ $property->property_bedrooms }}</td>
                        <td class="px-2 py-1">{{ $property->property_bathrooms }}</td>
                        <td class="px-2 py-1">{{ $property->property_hoa ?? '-' }}</td>
                        <td class="px-2 py-1">{{ $property->property_building_size_m2 }}</td>
                        <td class="px-2 py-1">{{ $property->property_lot_size_m2 }} </td>
                        <td class="px-2 py-1">{{ $property->property_no_of_floors }}</td>
                        @foreach ($featuresList as $feature)
                            <td class="px-2 py-1 text-center">
                                {{ $property->features->contains('id', $feature->id) ? '✔' : 'X' }}
                            </td>
                        @endforeach
                        <td class="px-2 py-1">
                        <a target="_blank" href="{{ route('property.export', $property) }}"
                           class="">
                            <x-heroicon-o-printer class="w-5 h-5" />
                        </a>
                        </td>

                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="mt-4 p-4">
                {{ $results->links() }}
            </div>

        </div>

    @else
        <p class="text-sm text-gray-500 mt-4">No results.</p>
    @endif
</div>
