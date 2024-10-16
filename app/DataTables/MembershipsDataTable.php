<?php

namespace App\DataTables;

use App\Models\Membership;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class MembershipsDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('user', function ($membership) {
                return $membership->user->email;
            })
            ->addColumn('package', function ($membership) {
                return $membership->package->name;
            })
            ->addColumn('type', function ($membership) {
                return ucfirst($membership->package->type);
            })
            ->addColumn('started', function ($membership) {
                return $membership->created_at->format('Y-m-d');
            })
            ->addColumn('ends', function ($membership) {
                return $membership->end_at->format('Y-m-d');
            })
            ->addColumn('action', 'memberships.action')
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Membership $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('memberships-table')
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
        return 'Memberships_' . date('YmdHis');
    }
}
