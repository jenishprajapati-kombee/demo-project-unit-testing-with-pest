
            <div class="flex flex-col w-full">
                <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4 gap-4">
                    
@if (Gate::allows('view-role'))
<a wire:navigate href="/role" data-testid="role" class="group flex flex-col h-24 lg:h-30 items-center justify-center p-2 bg-white dark:bg-zinc-700 rounded-lg transition-all duration-300 border border-gray-200 dark:border-zinc-600 hover:bg-blue-100 dark:hover:bg-zinc-600 hover:-translate-y-1">
    <div class="mb-2">
        <flux:icon name="users" class="h-8 lg:h-10 w-8 lg:w-10 text-blue-600 dark:text-blue-400" />
    </div>
    <h3 class="text-xs lg:text-base font-semibold text-gray-800 dark:text-gray-200 group-hover:text-blue-700 dark:group-hover:text-blue-300 transition-colors duration-300">
        @lang('messages.side_menu.role')
    </h3>
</a>
@endif
@if (Gate::allows('view-user'))
<a wire:navigate href="/user" data-testid="user" class="group flex flex-col h-24 lg:h-30 items-center justify-center p-2 bg-white dark:bg-zinc-700 rounded-lg transition-all duration-300 border border-gray-200 dark:border-zinc-600 hover:bg-blue-100 dark:hover:bg-zinc-600 hover:-translate-y-1">
    <div class="mb-2">
        <flux:icon name="users" class="h-8 lg:h-10 w-8 lg:w-10 text-blue-600 dark:text-blue-400" />
    </div>
    <h3 class="text-xs lg:text-base font-semibold text-gray-800 dark:text-gray-200 group-hover:text-blue-700 dark:group-hover:text-blue-300 transition-colors duration-300">
        @lang('messages.side_menu.user')
    </h3>
</a>
@endif
@if (Gate::allows('view-brand'))
<a wire:navigate href="/brand" data-testid="brand" class="group flex flex-col h-24 lg:h-30 items-center justify-center p-2 bg-white dark:bg-zinc-700 rounded-lg transition-all duration-300 border border-gray-200 dark:border-zinc-600 hover:bg-blue-100 dark:hover:bg-zinc-600 hover:-translate-y-1">
    <div class="mb-2">
        <flux:icon name="users" class="h-8 lg:h-10 w-8 lg:w-10 text-blue-600 dark:text-blue-400" />
    </div>
    <h3 class="text-xs lg:text-base font-semibold text-gray-800 dark:text-gray-200 group-hover:text-blue-700 dark:group-hover:text-blue-300 transition-colors duration-300">
        @lang('messages.side_menu.brand')
    </h3>
</a>
@endif
@if (Gate::allows('view-product'))
<a wire:navigate href="/product" data-testid="product" class="group flex flex-col h-24 lg:h-30 items-center justify-center p-2 bg-white dark:bg-zinc-700 rounded-lg transition-all duration-300 border border-gray-200 dark:border-zinc-600 hover:bg-blue-100 dark:hover:bg-zinc-600 hover:-translate-y-1">
    <div class="mb-2">
        <flux:icon name="users" class="h-8 lg:h-10 w-8 lg:w-10 text-blue-600 dark:text-blue-400" />
    </div>
    <h3 class="text-xs lg:text-base font-semibold text-gray-800 dark:text-gray-200 group-hover:text-blue-700 dark:group-hover:text-blue-300 transition-colors duration-300">
        @lang('messages.side_menu.product')
    </h3>
</a>
@endif<!-- Dynamic blocks will be inserted here -->
                </div>
            </div>