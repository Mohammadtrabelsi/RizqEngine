<?php

namespace Tests\Feature;

use App\Livewire\Products\ProductIndex;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use App\Services\ProductService;
use App\Services\Tags\ProductTagService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Tags\Tag;
use Tests\Concerns\SeedsStoreSettings;
use Tests\TestCase;

class ProductTagsTest extends TestCase
{
    use RefreshDatabase;
    use SeedsStoreSettings;

    private function userWith(array $permissions): User
    {
        $user = User::factory()->create();

        foreach ($permissions as $permission) {
            $user->givePermissionTo(Permission::findOrCreate($permission, 'web'));
        }

        return $user;
    }

    public function test_input_is_parsed_into_unique_trimmed_labels(): void
    {
        $labels = app(ProductTagService::class)->parse(' Promo, seasonal ;promo,, Fragile  item ،عرض ');

        $this->assertSame(['Promo', 'seasonal', 'Fragile item', 'عرض'], $labels);
    }

    public function test_input_is_capped_in_count_and_length(): void
    {
        $service = app(ProductTagService::class);

        $labels = $service->parse(implode(',', range(1, 40)));
        $this->assertCount(ProductTagService::MAX_TAGS, $labels);

        $long = $service->parse(str_repeat('x', 80));
        $this->assertSame(ProductTagService::MAX_LENGTH, mb_strlen($long[0]));
    }

    public function test_syncing_creates_tags_once_and_replaces_the_set(): void
    {
        $service = app(ProductTagService::class);
        $first = Product::factory()->create();
        $second = Product::factory()->create();

        $service->sync($first, 'promo, seasonal');
        $service->sync($second, 'Promo');

        $this->assertSame(2, Tag::getWithType(Product::TAG_TYPE)->count(), 'An existing tag is reused, not duplicated.');
        $this->assertEqualsCanonicalizing(['promo', 'seasonal'], $first->tags->pluck('name')->all());
        $this->assertSame(['promo'], $second->tags->pluck('name')->all());

        $service->sync($first, 'fragile');
        $this->assertSame(['fragile'], $first->fresh()->tags->pluck('name')->all());

        $service->sync($first, '');
        $this->assertCount(0, $first->fresh()->tags);
    }

    public function test_tags_are_scoped_to_the_product_type(): void
    {
        Tag::findOrCreate('promo', 'other-type');

        $product = Product::factory()->create();
        app(ProductTagService::class)->sync($product, 'promo');

        $this->assertSame(1, Tag::getWithType(Product::TAG_TYPE)->count());
        $this->assertSame(Product::TAG_TYPE, $product->tags->first()->type);
    }

    public function test_a_tag_created_in_one_locale_is_found_in_another(): void
    {
        $service = app(ProductTagService::class);

        app()->setLocale('fr');
        $tag = $service->findOrCreate('promo');

        app()->setLocale('en');
        $this->assertTrue($service->findOrCreate('promo')->is($tag));
        $this->assertSame('promo', $tag->fresh()->getTranslation('name', 'ar'));
    }

    public function test_tags_are_saved_from_the_product_form(): void
    {
        $this->seedStoreSettings();
        $this->actingAs($this->userWith(['create_products', 'edit_products']));

        $category = Category::factory()->create();
        $supplier = Supplier::factory()->create();

        $this->post(route('products.store'), [
            'product_name' => 'Tagged widget',
            'product_code' => 4242,
            'product_barcode_symbology' => 'C128',
            'product_unit' => 'pcs',
            'product_quantity' => 5,
            'product_cost' => 2,
            'product_price' => 4,
            'product_stock_alert' => 10,
            'category_id' => $category->id,
            'supplier_id' => $supplier->id,
            'product_tags' => 'promo, seasonal',
        ])->assertRedirect(route('products.index'));

        $product = Product::where('product_code', 4242)->firstOrFail();
        $this->assertEqualsCanonicalizing(['promo', 'seasonal'], $product->tags->pluck('name')->all());

        // Updating without the field leaves tags untouched.
        app(ProductService::class)->update($product, ['product_stock_alert' => 12]);
        $this->assertCount(2, $product->fresh()->tags);
    }

    public function test_tag_input_is_validated(): void
    {
        $this->seedStoreSettings();
        $this->actingAs($this->userWith(['create_products']));

        $this->post(route('products.store'), ['product_tags' => str_repeat('a', 1001)])
            ->assertSessionHasErrors('product_tags');
    }

    public function test_the_product_index_filters_by_tag(): void
    {
        $this->seedStoreSettings();
        $this->actingAs($this->userWith(['access_products']));

        $service = app(ProductTagService::class);
        $tagged = Product::factory()->create(['product_name' => 'Tagged one']);
        $other = Product::factory()->create(['product_name' => 'Untagged one']);
        $service->sync($tagged, 'promo');

        $promo = Tag::findFromString('promo', Product::TAG_TYPE);

        Livewire::test(ProductIndex::class)
            ->set('tag', (string) $promo->id)
            ->assertSee('Tagged one')
            ->assertDontSee('Untagged one')
            ->assertSee('#promo');
    }
}
