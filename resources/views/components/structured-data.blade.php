{{-- JSON-LD structured data block. Encoding/escaping lives in StructuredDataService. --}}
@props(['data' => []])

@if (! empty($data))
    {!! app(\App\Services\Seo\StructuredDataService::class)->toScript($data) !!}
@endif
