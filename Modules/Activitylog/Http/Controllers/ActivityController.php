<?php

namespace Modules\Activitylog\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Spatie\Activitylog\Models\Activity;

class ActivityController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('access_activity_logs'), 403);

        $activities = Activity::with('causer')->latest()->paginate(15);

        return view('activitylog::index', compact('activities'));
    }

    public function show(Activity $activity)
    {
        abort_if(Gate::denies('access_activity_logs'), 403);

        $activity->load(['causer', 'subject']);

        return view('activitylog::show', compact('activity'));
    }

    public function destroy(Activity $activity)
    {
        abort_if(Gate::denies('delete_activity_logs'), 403);

        $activity->delete();

        session()->flash('warning', trans('activitylog.activity-log-deleted'));

        return redirect()->route('activity-logs.index');
    }

    public function clear(Request $request)
    {
        abort_if(Gate::denies('delete_activity_logs'), 403);

        Activity::query()->delete();

        session()->flash('warning', trans('activitylog.activity-logs-cleared'));

        return redirect()->route('activity-logs.index');
    }
}
