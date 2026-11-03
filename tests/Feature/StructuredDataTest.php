<?php

namespace Tests\Feature;

use App\Services\Seo\StructuredDataService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SeedsStoreSettings;
use Tests\TestCase;

class StructuredDataTest extends TestCase
{
    use RefreshDatabase;
    use SeedsStoreSettings;

    /**
     * @return array<string, mixed>
     */
    private function jsonLdFrom(string $html): array
    {
        preg_match_all('#<script type="application/ld\+json">(.*?)</script>#s', $html, $matches);

        $this->assertCount(1, $matches[1], 'Exactly one JSON-LD block is rendered (no duplicates).');

        return json_decode($matches[1][0], true, flags: JSON_THROW_ON_ERROR);
    }

    public function test_the_landing_page_publishes_organization_and_website_data(): void
    {
        $this->seedStoreSettings(['company_name' => 'Rizq Market', 'company_email' => 'hello@rizq.test', 'company_phone' => '+216 70 000 000']);

        $data = $this->jsonLdFrom($this->get(route('welcome'))->assertOk()->getContent());

        $this->assertSame('https://schema.org', $data['@context']);
        $types = array_column($data['@graph'], '@type');
        $this->assertSame(['Organization', 'WebSite'], $types);

        [$organization, $website] = $data['@graph'];
        $this->assertSame('Rizq Market', $organization['name']);
        $this->assertSame('hello@rizq.test', $organization['email']);
        $this->assertSame('+216 70 000 000', $organization['telephone']);
        $this->assertSame('address', $organization['address']['streetAddress']);
        $this->assertSame($organization['@id'], $website['publisher']['@id']);
        $this->assertSame(url('/'), $website['url']);
    }

    public function test_empty_values_are_omitted_and_a_fallback_name_is_used(): void
    {
        $data = app(StructuredDataService::class)->forLandingPage();
        $organization = $data['@graph'][0];

        $this->assertSame(config('app.name'), $organization['name']);
        $this->assertArrayNotHasKey('email', $organization);
        $this->assertArrayNotHasKey('address', $organization);
    }

    public function test_admin_entered_values_cannot_break_out_of_the_script_tag(): void
    {
        $this->seedStoreSettings(['company_name' => 'Evil</script><script>alert(1)</script>']);

        $html = $this->get(route('welcome'))->assertOk()->getContent();

        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
        $this->assertSame('Evil</script><script>alert(1)</script>', $this->jsonLdFrom($html)['@graph'][0]['name']);
    }

    public function test_the_sign_in_page_carries_no_structured_data(): void
    {
        $this->get(route('redesign.login'))->assertOk()->assertDontSee('application/ld+json', false);
    }
}
