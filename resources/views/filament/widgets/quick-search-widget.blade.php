<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Quick Property Search
        </x-slot>

        <div class="space-y-4">
            <x-filament::input.wrapper>
                <x-filament::input
                    type="search"
                    wire:model.live.debounce.500ms="search"
                    placeholder="Search properties by title..."
                />
            </x-filament::input.wrapper>

            {{ $this->table }}
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
