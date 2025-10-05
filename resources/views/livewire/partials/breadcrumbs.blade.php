@php
use Illuminate\Support\Facades\Route;
use Diglactic\Breadcrumbs\Breadcrumbs;

$breadcrumbs = Breadcrumbs::generate(Route::currentRouteName(), ...Route::current()->parameters());
@endphp

<div>
    @if ($breadcrumbs->count() > 0)
    <header class="relative w-full pl-4 py-3 flex items-center">
        <flux:breadcrumbs>
            @foreach ($breadcrumbs as $index => $crumb)
                @php
                    $isCurrent = request()->url() === $crumb->url;
                    $tooltipText = $crumb->data['tooltip'] ?? strip_tags($crumb->title);
                @endphp

                @if ($index === 0)
                    <flux:tooltip :content="$tooltipText" position="bottom">
                        <flux:breadcrumbs.item href="{{ $crumb->url }}" icon="home">
                            {!! $crumb->title !!}
                        </flux:breadcrumbs.item>
                    </flux:tooltip>
                @elseif ($isCurrent)
                    <flux:breadcrumbs.item active="true">
                        {!! $crumb->title !!}
                    </flux:breadcrumbs.item>
                @else
                    <flux:tooltip :content="$tooltipText" position="bottom">
                        <flux:breadcrumbs.item href="{{ $crumb->url }}">
                            {!! $crumb->title !!}
                        </flux:breadcrumbs.item>
                    </flux:tooltip>
                @endif
            @endforeach
        </flux:breadcrumbs>
    </header>
    @endif
</div>
