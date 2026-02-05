@props([
    'id',
    'label',
    'required' => false,
    'disabled' => false,
    'testid' => null,
    'searchable' => false,
    'placeholder' => __('Select an option'),
])

@php
    $wireModel = $attributes->wire('model');
    // Get the base property name for flux:error 
    $errorName = $wireModel->value() ?: (string) $attributes->whereStartsWith('wire:model')->first();
@endphp

<div class="flex-1">
    <flux:field>
       @if ($label)
            <flux:label for="{{ $id }}" :required="$required">
                {{ $label }}
                @if ($required)
                    <span class="text-red-500">*</span>
                @endif
            </flux:label>
        @endif

        @if ($searchable)
            <div x-data="{
                open: false,
                selected: @entangle($wireModel),
                searchTerm: '',
                options: [],
                get filteredOptions() {
                    if (!this.searchTerm) return this.options;
                    const q = this.searchTerm.toLowerCase().trim();
                    return this.options.filter(o => o.label.toLowerCase().includes(q));
                },
                get selectedLabel() {
                    const found = this.options.find(o => o.value == this.selected);
                    return found ? found.label : '{{ $placeholder }}';
                },
                init() {
                    const select = this.$refs.hiddenSelect;
                    if (select) {
                        this.options = Array.from(select.options)
                            .map(o => ({ value: o.value, label: o.text.trim() }))
                            .filter(o => o.value !== '');
                    }
                    this.$watch('open', v => {
                        if (v) setTimeout(() => this.$refs.searchInput?.focus(), 50);
                        else this.searchTerm = '';
                    });
                }
            }" x-effect="init()" class="relative" @click.outside="open = false">
                {{-- Capture options from slot --}}
                <select x-ref="hiddenSelect" class="hidden">{{ $slot }}</select>

                <button type="button" :id="$id" @click="open = !open" @disabled($disabled)
                    data-testid="{{ $testid ?? $id }}"
                    class="flex h-10 w-full justify-between items-center border border-zinc-200 bg-white rounded-lg px-3 py-2 text-left shadow-sm hover:border-zinc-300 focus:outline-none focus:ring-2 focus:ring-black transition duration-150 ease-in-out disabled:bg-gray-100 disabled:cursor-not-allowed">
                    <span class="truncate text-zinc-800 text-sm" x-text="selectedLabel"></span>
                    <flux:icon.chevron-up-down class="w-4 h-4 ml-2 text-zinc-400 shrink-0" />
                </button>

                <div x-show="open" x-transition.origin.top @click.stop
                    class="absolute z-50 mt-1 w-full rounded-lg border border-zinc-200 bg-white shadow-lg max-h-60 overflow-auto focus:outline-none">
                    <div class="sticky top-0 z-10 bg-white border-b border-zinc-200 p-2">
                        <input type="text" x-model="searchTerm" x-ref="searchInput" placeholder="{{ __('Search...') }}"
                            class="w-full px-3 py-2 border border-zinc-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-black text-sm" />
                    </div>

                    <div class="py-1">
                        <template x-for="option in filteredOptions" :key="option.value">
                            <div @click="selected = option.value; open = false"
                                class="px-3 py-2 text-sm cursor-pointer hover:bg-gray-100 transition duration-150"
                                :class="{ 'bg-gray-50 font-semibold': selected == option.value }">
                                <span x-text="option.label"></span>
                            </div>
                        </template>
                        <div x-show="filteredOptions.length === 0" class="px-3 py-2 text-sm text-gray-500">
                            {{ __('No results found') }}
                        </div>
                    </div>
                </div>
            </div>
        @else
            <flux:select :id="$id" :data-testid="$testid ?? $id"
                class="{{ $disabled ? 'cursor-not-allowed bg-gray-100' : 'cursor-pointer' }}"
                :required="$required" {{ $attributes->whereStartsWith('wire:model') }}>
                {{ $slot }}
            </flux:select>
        @endif

        @if ($errorName)
            <flux:error :name="$errorName" :data-testid="$testid ? $testid . '_error' : $id . '_error'" />
        @endif
    </flux:field>
</div>
