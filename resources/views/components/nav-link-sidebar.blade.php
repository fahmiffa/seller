@props(['active'])

@php
$classes = ($active ?? false)
? 'flex items-center w-full h-12 px-3 text-sm font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-50/50 dark:bg-indigo-900/20 rounded-xl border-l-[3px] border-indigo-600 dark:border-indigo-400 group transition-all duration-300'
: 'flex items-center w-full h-12 px-3 text-sm font-semibold text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-xl group transition-all duration-300';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    @if(isset($icon))
    <div class="shrink-0 w-10 h-10 flex items-center justify-center {{ ($active ?? false) ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white transition-colors duration-300' }}">
        {{ $icon }}
    </div>
    @endif

    <div class="flex-1 flex items-center justify-between opacity-0 group-hover/sidebar:opacity-100 transition-all duration-300 overflow-hidden whitespace-nowrap">
        <span class="ms-3">{{ $slot }}</span>
        @if($active ?? false)
        <div class="ms-auto pe-2">
            <div class="h-1.5 w-1.5 rounded-full bg-indigo-600 dark:bg-indigo-400 animate-pulse"></div>
        </div>
        @endif
    </div>
</a>