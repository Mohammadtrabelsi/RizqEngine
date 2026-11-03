<?php

namespace App\Services\Seo;

use Spatie\SchemaOrg\Graph;
use Spatie\SchemaOrg\Schema;
use Throwable;

/**
 * Builds Schema.org structured data (JSON-LD) with spatie/schema-org.
 *
 * Only types that match public content are produced: the application has a
 * single public page (the landing page), so that is an Organization (the
 * store, from the admin Settings) and the WebSite it publishes. Back-office
 * pages are behind authentication and get no structured data.
 */
class StructuredDataService
{
    /**
     * Organization + WebSite graph for the public landing page.
     *
     * @return array<string, mixed>
     */
    public function forLandingPage(): array
    {
        $identity = $this->identity();
        $homeUrl = url('/');
        $organizationId = $homeUrl.'/#organization';

        $graph = new Graph;

        $organization = $graph->organization()
            ->setProperty('@id', $organizationId)
            ->name($identity['name'])
            ->url($homeUrl)
            ->logo($identity['logo']);

        if ($identity['email']) {
            $organization->email($identity['email']);
        }

        if ($identity['phone']) {
            $organization->telephone($identity['phone']);
        }

        if ($identity['address']) {
            $organization->address(Schema::postalAddress()->streetAddress($identity['address']));
        }

        $graph->webSite()
            ->setProperty('@id', $homeUrl.'/#website')
            ->name((string) config('app.name'))
            ->url($homeUrl)
            ->inLanguage(str_replace('_', '-', app()->getLocale()))
            ->publisher(Schema::organization()->setProperty('@id', $organizationId));

        return $graph->toArray();
    }

    /**
     * Render data as a JSON-LD <script> tag. "<", ">" and "&" are escaped so
     * admin-entered values can never close the script element.
     *
     * @param  array<string, mixed>  $data
     */
    public function toScript(array $data): string
    {
        $json = json_encode(
            $data,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP
        );

        return '<script type="application/ld+json">'.$json.'</script>';
    }

    /**
     * Store identity from the singleton settings record, with safe fallbacks
     * when settings are missing (fresh install) or unreadable.
     *
     * @return array{name: string, email: ?string, phone: ?string, address: ?string, logo: string}
     */
    private function identity(): array
    {
        try {
            $settings = settings();
        } catch (Throwable) {
            $settings = null;
        }

        return [
            'name' => (string) (($settings?->company_name ?: $settings?->client_name) ?: config('app.name')),
            'email' => $settings?->company_email ?: null,
            'phone' => $settings?->company_phone ?: null,
            'address' => $settings?->company_address ?: null,
            'logo' => client_logo_url() ?? asset('images/logo.png'),
        ];
    }
}
