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
                    <span class="dark:text-blue-500 text-mc_primary_color dark:bg-blue-500/30 bg-mc_primary_color/30 p-2 rounded-full text-sm font-bold">
                        {!! $crumb->title !!}
                    </span>
                @else
                    <a href="{{ $crumb->url }}" wire:navigate class="dark:text-blue-500 text-mc_primary_color dark:bg-blue-500/30 bg-mc_primary_color/30 p-2 rounded-full text-sm font-semibold underline-none hover:underline">
                        {!! $crumb->title !!}
                    </a>
                @endif

                @if (!$loop->last)
                    <span class="mx-2 dark:text-blue-500 text-mc_primary_color font-bold">
                        <flux:icon.chevron-right class="size-6"/>
                    </span>
                @endif
            @endforeach
        </header>
    @endif
</div>
