<?php

namespace App\Livewire\ActivityLogs;

use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;

class ActivityLogIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    #[Url(as: 'q')]
    public string $search = '';

    public function mount(): void
    {
        abort_if(Gate::denies('access_activity_logs'), 403);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        abort_if(Gate::denies('delete_activity_logs'), 403);

        Activity::findOrFail($id)->delete();

        session()->flash('warning', trans('activitylog.activity-log-deleted'));
    }

    public function clear(): void
    {
        abort_if(Gate::denies('delete_activity_logs'), 403);

        Activity::query()->delete();

        session()->flash('warning', trans('activitylog.activity-logs-cleared'));

        $this->resetPage();
    }

    public function render()
    {
        $activities = Activity::query()
            ->with('causer')
            ->when($this->search, function ($query) {
                $term = '%'.$this->search.'%';
                $query->where('description', 'like', $term)
                    ->orWhere('log_name', 'like', $term);
            })
            ->latest()
            ->paginate(15);

        return view('livewire.activity-logs.activity-log-index', compact('activities'));
    }
}
