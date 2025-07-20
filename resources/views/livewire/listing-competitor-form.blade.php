@php
    use Carbon\Carbon;
    use Illuminate\Support\Str;
@endphp

<div class="space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium">Listing Company</label>
            <select wire:model="companyId" class="block w-full rounded-md border border-gray-300 shadow-sm focus:ring focus:ring-primary-300 focus:border-primary-300">
                <option value="">-- All --</option>
                @foreach($companies as $company)
                    <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium">Property Status</label>
            <select wire:model="statusId" class="block w-full rounded-md border border-gray-300 shadow-sm focus:ring focus:ring-primary-300 focus:border-primary-300">
                <option value="">-- All --</option>
                @foreach($statuses as $status)
                    <option value="{{ $status->id }}">{{ $status->status_name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium">Reference URL</label>
            <input type="text" wire:model.debounce.500ms="referenceLink" class="block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-primary-300 focus:border-primary-300" >
        </div>

        <div class="flex items-end">
            <button wire:click="search"
                    class="bg-primary-600 text-white px-4 py-2 rounded shadow hover:bg-primary-700 transition">
                Search
            </button>
        </div>
    </div>

    <table class="w-full table-auto text-sm text-left border border-gray-300">
        <thead>
        <tr class="bg-gray-100">
            <th class="border px-3 py-2 text-left">Listing Company</th>
            <th class="border px-3 py-2 text-left">Property Title</th>
            <th class="border px-3 py-2 text-left">Added Date</th>
            <th class="border px-3 py-2 text-left">Property Status</th>
            <th class="border px-3 py-2 text-left">Reference Link</th>
            <th class="border px-3 py-2 text-left">Operations</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($results as $competitor)
            <tr>
                <td class="border px-3 py-2">{{ $competitor->company->company_name ?? '-' }}</td>
                <td class="border px-3 py-2">

                    <a href="{{ $competitor->property->slug
            ? route('property.public.show',     ['slug' => $competitor->property->slug])
            : route('property.public.showById', ['id' => $competitor->property->id]) }}"
                       class="text-primary-600 hover:underline font-semibold">
                        {{ $competitor->property->property_title }}
                    </a>
                </td>
                <td class="border px-3 py-2">
                    {{ \Carbon\Carbon::parse($competitor->property->property_added_date)->format('Y-m-d') }}
                    </td>
                <td class="border px-3 py-2">{{ $competitor->property->status->status_name ?? '-' }}</td>
                <td class="border px-3 py-2">
                    @if ($competitor->competitor_property_link)
                        <a href="{{ $competitor->competitor_property_link }}" target="_blank" class="text-blue-600 ">
                            {{ Str::limit($competitor->competitor_property_link, 40) }}
                        </a>
                    @else
                        -
                    @endif
                </td>
                <td class="border px-3 py-2 space-x-2">

                    <a href="{{ route('filament.admin.resources.properties.view', ['record' => $competitor->property->id]) }}"
                       class="fi-link group/link relative inline-flex items-center justify-center outline-none fi-size-sm fi-link-size-sm gap-1 fi-color-custom fi-color-primary fi-ac-action fi-ac-link-action">
                        <x-heroicon-o-eye class="w-5 h-5" />
                        View
                    </a>

                    <a href="{{ route('filament.admin.resources.properties.edit', ['record' => $competitor->property->id]) }}"
                       class="fi-link group/link relative inline-flex items-center justify-center outline-none fi-size-sm fi-link-size-sm gap-1 fi-color-custom fi-color-primary fi-ac-action fi-ac-link-action">
                        <x-heroicon-o-pencil class="w-5 h-5" />
                        Edit
                    </a>


                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="border px-3 py-2 text-center text-gray-500">No results</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div class="mt-4">
        {{ $results->links() }}
    </div>
</div>
