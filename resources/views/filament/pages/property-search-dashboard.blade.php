<x-filament::page>
    <div x-data="{ tab: 'search' }" class="space-y-6">

        <div class="flex space-x-4 border-b">
            <button
                @click="tab = 'search'"
                class="py-2 px-4 border-b-2"
                :class="tab === 'search' ? 'border-primary-500 text-primary-600 font-semibold' : 'border-transparent text-gray-500'"
            >
                MLS Search
            </button>

            <button
                @click="tab = 'otros'"
                class="py-2 px-4 border-b-2"
                :class="tab === 'otros' ? 'border-primary-500 text-primary-600 font-semibold' : 'border-transparent text-gray-500'"
            >
                -DMR-
            </button>
        </div>

        <div x-show="tab === 'search'" x-cloak>
            <livewire:property-search-form />
        </div>

        <div x-show="tab === 'otros'" x-cloak>
            <p class="text-sm text-gray-600">-DMR-</p>
        </div>

    </div>
</x-filament::page>
