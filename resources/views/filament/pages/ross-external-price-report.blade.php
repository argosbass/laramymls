<x-filament-panels::page>




    <div class="mb-6 rounded-xl border border-gray-200 bg-white p-5 shadow-sm">

        <div class="mb-6 flex flex-wrap items-center gap-2 p-4">

    <span class="mr-2 text-sm font-semibold text-gray-700">
        Show status:
    </span>

            @php
                $filters = [
                    '' => ['label' => 'All', 'count' => count($rows), 'active' => $resultFilter === null || $resultFilter === ''],
                    'Missing' => ['label' => 'Missing', 'count' => $this->stats['missing'], 'active' => $resultFilter === 'Missing'],
                    'Price Different' => ['label' => 'Price Different', 'count' => $this->stats['different'], 'active' => $resultFilter === 'Price Different'],
                    'OK' => ['label' => 'OK', 'count' => $this->stats['ok'], 'active' => $resultFilter === 'OK'],
                ];
            @endphp

            @foreach ($filters as $value => $filter)
                <button
                    wire:click="$set('resultFilter', '{{ $value }}')"
                    @class([
                        'rounded-full border px-4 py-2 text-sm font-medium transition-all duration-200',
                        'bg-primary-600 text-white border-primary-600 shadow-sm scale-105' => $filter['active'],
                        'bg-white text-gray-700 border-gray-300 hover:bg-gray-100 hover:border-gray-400 hover:shadow-sm' => ! $filter['active'],
                    ])
                >
                    {{ $filter['label'] }}
                    <span class="ml-1 text-xs opacity-80">
                ({{ $filter['count'] }})
            </span>
                </button>
            @endforeach

            <div class="ml-auto">
                <button
                    wire:click="downloadExcel"
                    class="rounded-lg border px-3 py-2 text-sm transition bg-danger-50 border-danger-200 hover:bg-danger-100"
                >
                    Export Excel
                </button>
            </div>

        </div>

        <div class="mt-5 grid gap-4 md:grid-cols-3 p-4">

            <div class="rounded-xl border border-red-200 bg-red-50 p-4">
                <div class="text-sm font-medium text-red-700">
                    Properties Missing
                </div>
                <div class="mt-2 text-3xl font-bold text-red-900">
                    {{ $this->stats['missing'] }}
                </div>
            </div>

            <div class="rounded-xl border border-yellow-200 bg-yellow-50 p-4">
                <div class="text-sm font-medium text-yellow-700">
                   Properties with Price Different
                </div>
                <div class="mt-2 text-3xl font-bold text-yellow-900">
                    {{ $this->stats['different'] }}
                </div>
            </div>

            <div class="rounded-xl border border-green-200 bg-green-50 p-4">
                <div class="text-sm font-medium text-green-700">
                    Properties OK
                </div>
                <div class="mt-2 text-3xl font-bold text-green-900">
                    {{ $this->stats['ok'] }}
                </div>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
            <tr>

                <th class="px-4 py-3 text-left font-semibold">
                    <button wire:click="sortBy('status')" class="hover:underline">
                        Status
                        @if($sortColumn === 'status') {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                        @else
                            {{ '*' }}
                        @endif
                    </button>
                </th>
                <th class="px-4 py-3 text-left font-semibold">
                    <button wire:click="sortBy('title')" class="hover:underline">
                        MLS Property
                        @if($sortColumn === 'title') {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                        @else
                            {{ '*' }}
                        @endif
                    </button>
                </th>

                <th class="px-4 py-3 text-left font-semibold">Reference Link</th>



                <th class="px-4 py-3 text-right font-semibold">
                    <button wire:click="sortBy('mlsPropertyStatus')" class="hover:underline">
                        MLS Property Status
                        @if($sortColumn === 'mlsPropertyStatus') {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                        @else
                            {{ '*' }}
                        @endif
                    </button>
                </th>


                <th class="px-4 py-3 text-right font-semibold">
                    <button wire:click="sortBy('rossPropertyStatus')" class="hover:underline">
                        ROSS Property Status
                        @if($sortColumn === 'rossPropertyStatus') {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                        @else
                            {{ '*' }}
                        @endif
                    </button>
                </th>

                <th class="px-4 py-3 text-right font-semibold">MLS Price</th>
                <th class="px-4 py-3 text-right font-semibold">ROSS Price</th>
            </tr>
            </thead>

            <tbody class="divide-y divide-gray-200">
            @forelse ($this->filteredRows as $row)
                <tr>
                    <td class="px-4 py-3">
                        @php
                            $color = match ($row['status']) {
                                'OK' => 'bg-green-100 text-green-700',
                                'Missing' => 'bg-red-100 text-red-700',
                                'Price Different' => 'bg-yellow-100 text-yellow-700',
                                default => 'bg-gray-100 text-gray-700',
                            };
                        @endphp

                        <span class="rounded-full px-2 py-1 text-xs font-medium {{ $color }}">
                                {{ $row['status'] }}
                            </span>
                    </td>

                    <td class="px-4 py-3">

                        @if (! empty($row['title']))
                            <a href="/admin/properties/{{ $row['id'] }}/edit" target="_blank" class="text-primary-600 hover:underline">
                                {{ Str::limit($row['title'], 70) }}
                            </a>
                        @endif

                    </td>



                    <td class="px-4 py-3">
                        @if (! empty($row['url']))
                            <a href="{{ $row['url'] }}" target="_blank" class="text-primary-600 hover:underline">
                                {{ Str::limit($row['url'], 70) }}
                            </a>
                        @else
                            -
                        @endif
                    </td>

                    <td class="px-4 py-3 text-right">

                        {{ $row['mlsPropertyStatus'] !== null ?  $row['mlsPropertyStatus'] : '-' }}

                    </td>
                    <td class="px-4 py-3 text-right">
                        {{ $row['rossPropertyStatus'] !== null ?  $row['rossPropertyStatus'] : '-' }}

                    </td>


                    <td class="px-4 py-3 text-right">
                        {{ $row['local_price'] !== null ? '$' . number_format($row['local_price'], 0) : '-' }}
                    </td>
                    <td class="px-4 py-3 text-right">
                        {{ '$' . number_format($row['external_price'], 0) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                        No hay datos para mostrar.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</x-filament-panels::page>
