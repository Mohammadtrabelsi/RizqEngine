<?php

namespace App\Http\Controllers;

use Illuminate\Support\Collection;

class DocumentationController extends Controller
{
    /**
     * Documentation overview: every topic grouped by its category.
     */
    public function index()
    {
        return view('documentation.index', [
            'groups' => $this->groupedSections(),
            'navGroups' => $this->groupedSections(),
        ]);
    }

    /**
     * A single documentation topic on its own page, with in-guide navigation
     * and previous / next links that follow the reading order.
     */
    public function show(string $section)
    {
        $sections = $this->sections();

        abort_unless($sections->has($section), 404, __('documentation.not_found'));

        $keys = $sections->keys()->values();
        $position = $keys->search($section);

        return view('documentation.show', [
            'key' => $section,
            'section' => $sections->get($section),
            'navGroups' => $this->groupedSections(),
            'previous' => $position > 0 ? $this->linkFor($sections, $keys->get($position - 1)) : null,
            'next' => $position < $keys->count() - 1 ? $this->linkFor($sections, $keys->get($position + 1)) : null,
        ]);
    }

    /**
     * All documentation topics, keyed by their slug, in reading order.
     */
    private function sections(): Collection
    {
        return collect(trans('documentation.sections'));
    }

    /**
     * Sections arranged under their group, preserving both group order (from
     * the `groups` list) and section order (from the `sections` list).
     */
    private function groupedSections(): Collection
    {
        $groupLabels = collect(trans('documentation.groups'));

        $byGroup = $this->sections()
            ->map(fn (array $section, string $key) => $section + ['key' => $key])
            ->groupBy('group');

        return $groupLabels
            ->map(fn (string $label, string $groupKey) => [
                'key' => $groupKey,
                'label' => $label,
                'sections' => $byGroup->get($groupKey, collect()),
            ])
            ->filter(fn (array $group) => $group['sections']->isNotEmpty())
            ->values();
    }

    /**
     * Compact link payload (slug + title) used for previous / next links.
     */
    private function linkFor(Collection $sections, string $key): array
    {
        return ['key' => $key, 'title' => $sections->get($key)['title']];
    }
}
