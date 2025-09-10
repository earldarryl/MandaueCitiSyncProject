@props(['active'])

<a {{ $attributes->merge(['class']) }}>
    {{ $slot }}
</a>
