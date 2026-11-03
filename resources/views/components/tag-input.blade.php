{{--
    Tag selector: a comma-separated text input with clickable suggestions.
    Presentation only — parsing and persistence live in ProductTagService.
    Clicking a suggestion appends it (handled by the delegated
    [data-tag-suggestion] listener in resources/js/app.js).

    Props:
      name         input name (default "product_tags")
      value        current comma-separated labels
      suggestions  iterable of existing tag labels
--}}
@props(['name' => 'product_tags', 'value' => '', 'suggestions' => []])

<div>
    <label for="{{ $name }}">{{ __('tags.label') }}</label>
    <input type="text" name="{{ $name }}" id="{{ $name }}" value="{{ $value }}"
           maxlength="1000" autocomplete="off"
           class="form-control @error($name) is-invalid @enderror"
           placeholder="{{ __('tags.placeholder') }}">
    <small class="form-text text-muted">{{ __('tags.help') }}</small>
    @error($name) <div class="invalid-feedback d-block">{{ $message }}</div> @enderror

    @if (count($suggestions) > 0)
        <div class="mt-2 d-flex flex-wrap gap-1" aria-label="{{ __('tags.suggestions') }}">
            @foreach ($suggestions as $suggestion)
                <button type="button" class="btn btn-outline btn-sm py-0 px-2"
                        data-tag-suggestion="{{ $suggestion }}" data-tag-target="{{ $name }}">
                    <i class="bi bi-tag"></i> {{ $suggestion }}
                </button>
            @endforeach
        </div>
    @endif
</div>
