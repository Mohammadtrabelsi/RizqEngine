<?php

namespace App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Spatie\Activitylog\Models\Activity;

/**
 * Read and maintenance access to the activity log.
 */
class ActivityLogService
{
    public function paginate(?string $search = null, int $perPage = 15): LengthAwarePaginator
    {
        return Activity::query()
            ->with('causer')
            ->when($search, function ($query) use ($search) {
                $term = '%'.$search.'%';
                $query->where('description', 'like', $term)
                    ->orWhere('log_name', 'like', $term);
            })
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Load an activity with its causer and subject relations, for the
     * detail view.
     */
    public function loadForShow(Activity $activity): Activity
    {
        return $activity->load(['causer', 'subject']);
    }

    /**
     * The attribute change-set of an activity, split into the keys that
     * changed and their old/new value maps, for the detail view.
     *
     * @return array{keys: array<int, string>, old: array<string, mixed>, new: array<string, mixed>}
     */
    public function changeSet(Activity $activity): array
    {
        $changes = $activity->attribute_changes ? $activity->attribute_changes->toArray() : [];
        $new = $changes['attributes'] ?? [];
        $old = $changes['old'] ?? [];

        return [
            'keys' => array_keys($new + $old),
            'old' => $old,
            'new' => $new,
        ];
    }

    public function delete(int $id): void
    {
        Activity::findOrFail($id)->delete();
    }

    public function deleteModel(Activity $activity): void
    {
        $activity->delete();
    }

    public function clear(): void
    {
        Activity::query()->delete();
    }
}
