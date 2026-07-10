<x-filament-panels::page>
    <div class="space-y-6">

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <label class="mb-2 block text-sm font-medium text-gray-700">
                URLs separated by comma
            </label>

            <textarea
                wire:model.defer="urls"
                rows="6"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                placeholder="https://site.com/page-1, https://site.com/page-2, https://site.com/page-3"
            ></textarea>

            <div class="mt-4 flex gap-3">
                <x-filament::button wire:click="search">
                    Search
                </x-filament::button>

                <x-filament::button
                    color="gray"
                    wire:click="resetSearch"
                >
                    Reset
                </x-filament::button>


            </div>
        </div>

        @if(count($results))


                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">

                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-lg font-semibold">
                            Results ({{ count($results) }})
                        </h2>

                        <x-filament::button
                            color="success"
                            icon="heroicon-o-arrow-down-tray"
                            wire:click="downloadCsv"
                        >
                            Download CSV
                        </x-filament::button>
                    </div>




                <div class="overflow-x-auto">
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
                        @foreach($results as $row)
                            <tr>
                                <td class="border px-3 py-2">{{ $row['company'] ?? '-' }}</td>
                                <td class="border px-3 py-2">{{ $row['property_title'] ?? 'Not Found' }}</td>
                                <td class="border px-3 py-2">{{ $row['added_date'] ?? '-' }}</td>
                                <td class="border px-3 py-2">{{ $row['status'] ?? '-' }}</td>

                                <td class="border px-3 py-2">
                                    <a href="{{ $row['reference_link'] }}" target="_blank">
                                        {{ Str::limit($row['reference_link'], 80) }}
                                    </a>
                                </td>

                                <td class="border px-3 py-2">
                                    @if($row['exists'])
                                        <a target="_blank"
                                           href="{{ $row['view_url'] }}"
                                           class="text-primary-600 hover:underline">
                                            View
                                        </a>

                                        |

                                        <a target="_blank"
                                           href="{{ $row['edit_url'] }}"
                                           class="text-primary-600 hover:underline">
                                            Edit
                                        </a>
                                    @else
                                        <span class="text-danger-600 font-semibold">
            Not Found
        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

    </div>
</x-filament-panels::page>
