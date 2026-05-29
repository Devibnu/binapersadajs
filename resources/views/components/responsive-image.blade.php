@if($hasWebp)
    <picture>
        <source srcset="@if(count($srcset) > 0){{ implode(', ', $srcset) }}@else{{ $webpSrc }}@endif" type="image/webp">
        <img {{ \Illuminate\Support\Arr::query($attrs) }} src="{{ $attrs['src'] }}">
    </picture>
@else
    <img {{ \Illuminate\Support\Arr::query($attrs) }}>
@endif
