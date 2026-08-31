<?php

namespace App\Livewire\ActivityLogs;

use App\Services\ActivityLogService;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

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

    public function delete(int $id, ActivityLogService $activities): void
    {
        abort_if(Gate::denies('delete_activity_logs'), 403);

        $activities->delete($id);

        session()->flash('warning', trans('activitylog.activity-log-deleted'));
    }

    public function clear(ActivityLogService $activities): void
    {
        abort_if(Gate::denies('delete_activity_logs'), 403);

        $activities->clear();

        session()->flash('warning', trans('activitylog.activity-logs-cleared'));

        $this->resetPage();
    }

    public function render(ActivityLogService $activities)
    {
        return view('livewire.activity-logs.activity-log-index', [
            'activities' => $activities->paginate($this->search),
        ]);
    }
}
