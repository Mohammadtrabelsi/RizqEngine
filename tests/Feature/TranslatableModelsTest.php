<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Services\CategoryService;
use App\Services\ProductCatalogService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TranslatableModelsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function creating_a_record_seeds_every_configured_locale_with_the_default_value()
    {
        config(['app.available_locales' => ['en', 'ar', 'fr']]);

        app()->setLocale('en');

        $category = Category::create([
            'category_code' => 'TR1',
            'category_name' => 'Beverages',
        ]);

        $this->assertSame('Beverages', $category->getTranslation('category_name', 'en'));
        $this->assertSame('Beverages', $category->getTranslation('category_name', 'ar'));
        $this->assertSame('Beverages', $category->getTranslation('category_name', 'fr'));
    }

    /** @test */
    public function switching_locale_returns_the_matching_translation()
    {
        config(['app.available_locales' => ['en', 'ar', 'fr']]);

        $category = Category::create([
            'category_code' => 'TR2',
            'category_name' => 'Drinks',
        ]);

        $category->setTranslation('category_name', 'ar', 'مشروبات');
        $category->setTranslation('category_name', 'fr', 'Boissons');
        $category->save();

        app()->setLocale('ar');
        $this->assertSame('مشروبات', $category->fresh()->category_name);

        app()->setLocale('fr');
        $this->assertSame('Boissons', $category->fresh()->category_name);

        app()->setLocale('en');
        $this->assertSame('Drinks', $category->fresh()->category_name);
    }

    /** @test */
    public function editing_one_locale_leaves_the_others_untouched()
    {
        config(['app.available_locales' => ['en', 'ar', 'fr']]);

        app()->setLocale('en');

        $category = Category::create([
            'category_code' => 'TR3',
            'category_name' => 'Snacks',
        ]);

        // A real Arabic translation is entered.
        $category->setTranslation('category_name', 'ar', 'وجبات خفيفة');
        $category->save();

        // Later the English value is edited from the English UI.
        app()->setLocale('en');
        $category->category_name = 'Snacks & More';
        $category->save();

        $fresh = $category->fresh();
        $this->assertSame('Snacks & More', $fresh->getTranslation('category_name', 'en'));
        $this->assertSame('وجبات خفيفة', $fresh->getTranslation('category_name', 'ar'));
        // French was never translated, so it keeps the original default.
        $this->assertSame('Snacks', $fresh->getTranslation('category_name', 'fr'));
    }

    /** @test */
    public function the_category_service_search_matches_content_in_any_language()
    {
        config(['app.available_locales' => ['en', 'ar', 'fr']]);

        $category = Category::create([
            'category_code' => 'TR4',
            'category_name' => 'Bakery',
        ]);
        $category->setTranslation('category_name', 'fr', 'Boulangerie');
        $category->save();

        $service = app(CategoryService::class);

        $this->assertTrue($service->paginate('Bakery')->contains('id', $category->id));
        $this->assertTrue($service->paginate('Boulangerie')->contains('id', $category->id));
    }

    /** @test */
    public function products_expose_translated_name_and_note()
    {
        config(['app.available_locales' => ['en', 'ar', 'fr']]);

        $category = Category::create(['category_code' => 'TR5', 'category_name' => 'Cat']);

        app()->setLocale('en');

        $product = Product::create([
            'category_id' => $category->id,
            'product_name' => 'Coffee',
            'product_code' => 'P-TR5',
            'product_quantity' => 1,
            'product_cost' => 1,
            'product_price' => 2,
            'product_stock_alert' => 0,
            'product_note' => 'Fresh',
        ]);

        // Every locale is defaulted on insert.
        $this->assertSame('Coffee', $product->getTranslation('product_name', 'ar'));
        $this->assertSame('Fresh', $product->getTranslation('product_note', 'fr'));

        // The catalog service returns models whose accessors are locale-aware.
        $product->setTranslation('product_name', 'fr', 'Café');
        $product->save();

        app()->setLocale('fr');
        $found = app(ProductCatalogService::class)->findOrFail($product->id);
        $this->assertSame('Café', $found->product_name);
    }
}
