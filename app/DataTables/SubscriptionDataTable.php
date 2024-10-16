<?php

namespace App\DataTables;

use App\Models\Subscription;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class SubscriptionDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('user', function ($subscription) {
                return $subscription->venue->user->email;
            })
            ->addColumn('package', function ($subscription) {
                return $subscription->package->name;
            })
            ->addColumn('type', function ($subscription) {
                return ucfirst($subscription->package->type);
            })
            ->addColumn('started', function ($subscription) {
                return $subscription->created_at->format('Y-m-d');
            })
            ->addColumn('ends', function ($subscription) {
                return $subscription->end_at->format('Y-m-d');
            })
            ->addColumn('action', 'subscriptions.action')
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(subscription $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('subscriptions-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->selectStyleSingle()
            ->scrollX(true)
            ->scrollCollapse(true) // Allow the table to resize when scrolling
            ->responsive(true) // Ensure table is responsive
            ->buttons([
                Button::make('excel'),
                Button::make('csv'),
                Button::make('pdf'),
                Button::make('print'),
                Button::make('reset'),
                Button::make('reload')
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center'),
            Column::make('user')->title('User'),
            Column::make('package')->title('Package'),
            Column::make('type')->title('Type'),
            Column::make('started')->title('Started'),
            Column::make('ends')->title('Ends'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'subscriptions_' . date('YmdHis');
    }
}
