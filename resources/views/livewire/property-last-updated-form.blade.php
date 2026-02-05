@php
    use Carbon\Carbon;
@endphp

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js@10.2.0/public/assets/styles/choices.min.css"/>
    <style>
        .choices {
            margin-bottom: 0 !important;
        }

        .choices__inner {
            background-color: white !important;
            border: 1px solid rgb(209 213 219) !important;
            border-radius: 0.375rem !important;
            box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05) !important;
            font-size: 0.875rem !important;
            min-height: 42px !important;
            padding: 0.3rem 0.75rem !important;
        }

        .choices__inner:focus {
            border-color: rgb(147 197 253) !important;
            box-shadow: 0 0 0 3px rgb(147 197 253 / 0.1) !important;
        }
        .choices[data-type*=select-one]::after {
            right: 15.5px;
        }

        .choices__list--dropdown {
            border: 1px solid rgb(209 213 219) !important;
            border-radius: 0.375rem !important;
        }
    </style>
@endpush

<div class="space-y-6 w-full">

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

            <div wire:ignore>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="typeId">Type</label>
                <select id="typeId" wire:model="typeId"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-primary-300 focus:border-primary-300">
                    <option value="">All</option>
                    @foreach ($types as $type)
                        <option value="{{ $type->id }}">{{ $type->type_name }}</option>
                    @endforeach
                </select>
            </div>

            <div wire:ignore>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="statusId">Status</label>
                <select id="statusId" wire:model="statusId"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-primary-300 focus:border-primary-300">
                    <option value="">All</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->id }}">{{ $status->status_name }}</option>
                    @endforeach
                </select>
            </div>

            <div wire:ignore>
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

            <div wire:ignore>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="authorId">Author</label>
                <select id="authorId" wire:model="authorId"
                        class="block w-full rounded-md border border-gray-300 shadow-sm focus:ring focus:ring-primary-300 focus:border-primary-300">
                    <option value="">All</option>
                    @foreach ($authors as $author)
                        <option value="{{ $author->id }}" style="{{ $location->depth == 1 ? 'font-weight: 700;' : '' }}">
                            {{ $author->name }}
                        </option>
                    @endforeach
                </select>
            </div>

        </div>



        <div class="mt-6">

            <button
                type="button"
                wire:click="search"
                    class="bg-primary-600 text-white px-4 py-2 rounded shadow hover:bg-primary-700 transition">
                Search
            </button>
            &nbsp;
            <button
                type="button"
                onclick="resetFilters()"
                class="bg-gray-400 text-white px-4 py-2 rounded-md shadow hover:bg-red-600 transition">
                Reset
            </button>
        </div>

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
                    <th class="px-2 py-1">Updated At</th>
                    <th class="px-2 py-1">Date Added</th>
                    <th class="px-2 py-1">Type</th>
                    <th class="px-2 py-1">Status</th>
                    <th class="px-2 py-1">Title</th>
                    <th class="px-2 py-1">Author</th>


                </tr>
                </thead>
                <tbody>
                @foreach ($results as $property)
                    <tr class="border-t">
                        <td class="px-2 py-1">{{ $property->id }}</td>
                        <td class="px-2 py-1">{{ $property->updated_at->format('Y-m-d H:i:s') }}</td>

                        <td class="px-2 py-1">{{ $property->created_at->format('Y-m-d') }}</td>

                        <td class="px-2 py-1">{{ $property->type->type_name ?? '-' }}</td>
                        <td class="px-2 py-1">{{ $property->status->status_name ?? '-' }}</td>

                        <td class="px-2 py-1">

                            <a target="_blank" href="{{ $property->slug
            ? route('property.public.show',     ['slug' => $property->slug])
            : route('property.public.showById', ['id' => $property->id]) }}"
                               class="text-primary-600 hover:underline font-semibold">
                                {{ $property->property_title }}
                            </a>


                        </td>

                        <td class="px-2 py-1">{{ $property->author->name ?? '-' }}</td>



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

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/choices.js@10.2.0/public/assets/scripts/choices.min.js"></script>
    <script>
        let typeChoices, statusChoices, locationChoices, authorChoices;

        document.addEventListener('DOMContentLoaded', function() {
            initializeChoices();
        });

        function initializeChoices() {
            // Destruir instancias previas si existen
            if (typeChoices) typeChoices.destroy();
            if (statusChoices) statusChoices.destroy();
            if (locationChoices) locationChoices.destroy();
            if (authorChoices) locationChoices.destroy();

            // Inicializar Choices para Type
            typeChoices = new Choices('#typeId', {
                searchEnabled: true,
                placeholder: false,
                placeholderValue: '',
                allowHTML: false
            });

            // Inicializar Choices para Status
            statusChoices = new Choices('#statusId', {
                searchEnabled: true,
                placeholder: false,
                placeholderValue: '',
                allowHTML: false
            });

            // Inicializar Choices para Location
            locationChoices = new Choices('#locationId', {
                searchEnabled: true,
                placeholder: false,
                placeholderValue: '',
                allowHTML: true,
                shouldSort: false,
                shouldSortItems: false,
            });


            // Inicializar Choices para author
            authorChoices = new Choices('#authorId', {
                searchEnabled: true,
                placeholder: false,
                placeholderValue: '',
                allowHTML: true
            });



        }


        function resetFilters() {
            // Resetear inputs de texto y número
            document.querySelectorAll('input[type="text"], input[type="number"]').forEach(el => el.value = '');

            // Resetear checkboxes
            document.querySelectorAll('input[type="checkbox"]').forEach(el => el.checked = false);

            // Resetear Choices.js
            typeChoices.setChoiceByValue('');
            statusChoices.setChoiceByValue('');
            locationChoices.setChoiceByValue('');
            authorChoices.setChoiceByValue('');

            // Resetear en Livewire (IMPORTANTE)
        @this.set('title', '');
        @this.set('propertyId', '');

        @this.set('typeId', '');
        @this.set('statusId', '');
        @this.set('locationId', '');

        @this.set('authorId', '');


        @this.call('resetFilters');

        @this.call('search');
        }

    </script>
@endpush
