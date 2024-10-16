<?php

namespace App\DataTables;

use App\Models\Voucher;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Auth;

class VouchersDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('claimed_at', function (Voucher $voucher) {
                return $voucher->claimed_at ? $voucher->claimed_at->format('Y-m-d') : '-'; // Handle null values
            })
            ->editColumn('created_at', function (Voucher $voucher) {
                return $voucher->created_at ? $voucher->created_at->format('Y-m-d') : '-'; // Handle null values
            })
            ->editColumn('updated_at', function (Voucher $voucher) {
                return $voucher->updated_at ? $voucher->updated_at->format('Y-m-d') : '-'; // Handle null values
            })
            ->addColumn('venue_id', function (Voucher $voucher) {
                return $voucher->venue->name ?? '-';
            })
            ->setRowId('id');
    }


    /**
     * Get the query source of dataTable.
     */
    public function query(Voucher $model): QueryBuilder
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            // Admin can view all vouchers
            return $model->newQuery()->with('venue');
        } else {
            // Non-admins can only view vouchers related to venues they own
            return $model->newQuery()
                ->whereHas('venue', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->with('venue');
        }
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('vouchers-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    //->dom('Bfrtip')
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
            Column::make('id'),
            Column::make('uuid'),
            Column::make('text'),
            Column::make('status'),
            Column::make('venue_id')->title('Venue'),
            Column::make('claimed_at'),
            Column::make('created_at'),
            Column::make('updated_at'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Vouchers_' . date('YmdHis');
    }
}
