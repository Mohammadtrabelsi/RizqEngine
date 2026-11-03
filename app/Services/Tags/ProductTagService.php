<?php

namespace App\Services\Tags;

use App\Http\Middleware\SetLocale;
use App\Models\Product;
use Illuminate\Support\Collection;
use Spatie\Tags\Tag;

/**
 * Free-form product labels (e.g. "seasonal", "fragile", "promo") built on
 * spatie/laravel-tags. Tags complement — they do not replace — the single
 * product category.
 *
 * Tags are created with the same label in every supported locale, so a tag
 * typed while the UI is in French is still found (and shown) in English or
 * Arabic. Labels can be translated later by editing the tag.
 */
class ProductTagService
{
    /** Maximum number of tags accepted on a single product. */
    public const MAX_TAGS = 20;

    /** Maximum length of a single tag label. */
    public const MAX_LENGTH = 50;

    /**
     * Split user input ("a, b; c" — Arabic comma accepted too) into a clean, de-duplicated list of labels.
     *
     * @param  string|array<int, string>|null  $input
     * @return array<int, string>
     */
    public function parse(string|array|null $input): array
    {
        $parts = is_array($input) ? $input : preg_split('/[,;،]/u', (string) $input);

        $labels = [];

        foreach ($parts ?: [] as $part) {
            $label = trim(preg_replace('/\s+/u', ' ', (string) $part) ?? '');

            if ($label === '') {
                continue;
            }

            $labels[mb_strtolower($label)] ??= mb_substr($label, 0, self::MAX_LENGTH);
        }

        return array_slice(array_values($labels), 0, self::MAX_TAGS);
    }

    /**
     * Replace the product's tags with the given labels, creating missing tags.
     *
     * @param  string|array<int, string>|null  $input
     */
    public function sync(Product $product, string|array|null $input): void
    {
        $index = $this->index();
        $tags = [];

        foreach ($this->parse($input) as $label) {
            $tags[] = $this->findOrCreate($label, $index);
        }

        $product->syncTagsWithType($tags, Product::TAG_TYPE);
    }

    /**
     * Comma-separated labels of the product's tags, for pre-filling a form.
     */
    public function asString(Product $product): string
    {
        return $product->tagsWithType(Product::TAG_TYPE)->pluck('name')->implode(', ');
    }

    /**
     * All product tags, alphabetically, for filter and suggestion lists.
     *
     * @return Collection<int, Tag>
     */
    public function all(): Collection
    {
        return Tag::getWithType(Product::TAG_TYPE)->sortBy(fn (Tag $tag) => mb_strtolower((string) $tag->name))->values();
    }

    /**
     * Find a product tag by label (case-insensitive, in any locale), or create
     * it with that label for every supported locale.
     *
     * @param  array<string, Tag>|null  $index  Pre-built lookup (see index()); built on demand.
     */
    public function findOrCreate(string $label, ?array &$index = null): Tag
    {
        $index ??= $this->index();
        $key = mb_strtolower($label);

        if (isset($index[$key])) {
            return $index[$key];
        }

        $locales = array_unique(array_merge([app()->getLocale()], $this->supportedLocales()));

        $tag = Tag::create([
            'name' => array_fill_keys($locales, $label),
            'type' => Product::TAG_TYPE,
        ]);

        return $index[$key] = $tag;
    }

    /**
     * Lower-cased label (every translation) => tag, for the product tag type.
     * Product tags are a small, bounded vocabulary, so one query per sync is
     * cheaper than a JSON lookup per label.
     *
     * @return array<string, Tag>
     */
    private function index(): array
    {
        $index = [];

        foreach (Tag::getWithType(Product::TAG_TYPE) as $tag) {
            foreach ($tag->getTranslations('name') as $translation) {
                $index[mb_strtolower((string) $translation)] ??= $tag;
            }
        }

        return $index;
    }

    /**
     * @return array<int, string>
     */
    private function supportedLocales(): array
    {
        return SetLocale::SUPPORTED_LOCALES;
    }
}
