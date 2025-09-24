@php
    use Illuminate\Support\Facades\Route;
    use Diglactic\Breadcrumbs\Breadcrumbs;

    $breadcrumbs = Breadcrumbs::generate(Route::currentRouteName(), ...Route::current()->parameters());
@endphp

<div>
    @if ($breadcrumbs->count() > 1)
        <header class="relative w-full pl-4 py-3 flex items-center">
            @foreach ($breadcrumbs as $index => $crumb)
                @php
                    $isCurrent = request()->url() === $crumb->url;
                @endphp

                @if ($isCurrent)
                    <span class="text-mc_primary_color dark:text-white bg-mc_primary_color/10 dark:bg-zinc-700/50 p-2 rounded-full text-[12px] font-bold">
                        {!! $crumb->title !!}
                    </span>
                @else
                    <a href="{{ $crumb->url }}" wire:navigate class="text-mc_primary_color dark:text-white bg-mc_primary_color/10 dark:bg-zinc-700/50 p-2 rounded-full text-[12px] font-semibold underline-none hover:underline">
                        {!! $crumb->title !!}
                    </a>
                @endif

                @if (!$loop->last)
                    <span class="mx-2 text-mc_primary_color dark:text-white font-bold">
                        <flux:icon.chevron-right class="size-4"/>
                    </span>
                @endif
            @endforeach
        </header>
    @endif
</div>
