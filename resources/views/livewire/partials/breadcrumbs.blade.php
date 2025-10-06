@php
use Illuminate\Support\Facades\Route;
use Diglactic\Breadcrumbs\Breadcrumbs;

$currentRoute = Route::currentRouteName();
$breadcrumbs = Breadcrumbs::generate($currentRoute, ...Route::current()->parameters());
@endphp

<div>
    @if ($breadcrumbs->count() > 0)
        <header class="relative w-full pl-4 py-3 flex items-center">
            <flux:breadcrumbs>
                @foreach ($breadcrumbs as $index => $crumb)
                    @php
                        $isCurrent = request()->url() === $crumb->url;
                        $tooltipText = $crumb->data['tooltip'] ?? strip_tags($crumb->title);

                        $isLast = $index === $breadcrumbs->count() - 1;
                    @endphp

                    @if ($isLast && $breadcrumbs->count() > 1)
                        <flux:breadcrumbs.item active="true">
                            {!! $crumb->title !!}
                        </flux:breadcrumbs.item>
                    @elseif ($isLast && $breadcrumbs->count() === 1)
                        @continue
                    @else
                        @if ($index === 0)
                            <flux:tooltip :content="$tooltipText" position="bottom">
                                <flux:breadcrumbs.item href="{{ $crumb->url }}" icon="home">
                                    {!! $crumb->title !!}
                                </flux:breadcrumbs.item>
                            </flux:tooltip>
                        @else
                            <flux:tooltip :content="$tooltipText" position="bottom">
                                <flux:breadcrumbs.item href="{{ $crumb->url }}">
                                    {!! $crumb->title !!}
                                </flux:breadcrumbs.item>
                            </flux:tooltip>
                        @endif
                    @endif
                @endforeach
            </flux:breadcrumbs>
        </header>
    @endif
</div>
