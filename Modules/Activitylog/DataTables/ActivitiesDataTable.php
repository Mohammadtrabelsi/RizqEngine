<?php

namespace Modules\Activitylog\DataTables;

use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ActivitiesDataTable extends DataTable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('causer', function ($data) {
                return $data->causer->name ?? 'System';
            })
            ->addColumn('subject', function ($data) {
                if (! $data->subject_type) {
                    return '<span class="text-muted">&mdash;</span>';
                }

                $model = Str::headline(class_basename($data->subject_type));

                return '<span class="badge badge-light">'.$model.' #'.$data->subject_id.'</span>';
            })
            ->addColumn('event', function ($data) {
                $badges = [
                    'created' => 'badge-success',
                    'updated' => 'badge-info',
                    'deleted' => 'badge-danger',
                    'restored' => 'badge-warning',
                ];

                $class = $badges[$data->event] ?? 'badge-secondary';

                return '<span class="badge '.$class.'">'.($data->event ?? 'n/a').'</span>';
            })
            ->addColumn('action', function ($data) {
                return view('activitylog::partials.actions', compact('data'));
            })
            ->editColumn('created_at', function ($data) {
                return $data->created_at->format('d M, Y H:i');
            })
            ->rawColumns(['subject', 'event', 'action']);
    }

    public function query(Activity $model)
    {
        return $model->newQuery()->with('causer')->latest();
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('activity-logs-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom("<'row'<'col-md-3'l><'col-md-5 mb-2'B><'col-md-4'f>> .
                                'tr' .
                                <'row'<'col-md-5'i><'col-md-7 mt-2'p>>")
            ->orderBy(0, 'desc')
            ->buttons(
                Button::make('excel')
                    ->text('<i class="bi bi-file-earmark-excel-fill"></i> Excel'),
                Button::make('print')
                    ->text('<i class="bi bi-printer-fill"></i> Print'),
                Button::make('reset')
                    ->text('<i class="bi bi-x-circle"></i> Reset'),
                Button::make('reload')
                    ->text('<i class="bi bi-arrow-repeat"></i> Reload')
            );
    }

    protected function getColumns()
    {
        return [
            Column::make('created_at')
                ->title('Date')
                ->className('text-center align-middle'),

            Column::make('log_name')
                ->title('Module')
                ->className('text-center align-middle'),

            Column::computed('event')
                ->className('text-center align-middle'),

            Column::make('description')
                ->className('text-center align-middle'),

            Column::computed('subject')
                ->className('text-center align-middle'),

            Column::computed('causer')
                ->title('User')
                ->className('text-center align-middle'),

            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->className('text-center align-middle'),
        ];
    }

    protected function filename(): string
    {
        return 'ActivityLogs_'.date('YmdHis');
    }
}
