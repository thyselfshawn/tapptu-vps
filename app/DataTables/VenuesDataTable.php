<?php

namespace App\DataTables;

use App\Models\Venue;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class VenuesDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $dataTable = (new EloquentDataTable($query))
            ->addColumn('action', function (Venue $venue) {
                return view('venues.action', ['item' => $venue]);
            })
            ->addColumn('venue', function (Venue $venue) {
                return '<a href="' . route('venues.show', $venue->slug) . '">
                            <img src="' . route('guest.venues.image', ['filename' => $venue->logo]) .'" alt="Venue Logo" class="img-fluid rounded-circle" height="30" width="30" loading="lazy">
                            ' . $venue->name . '
                        </a>';
            })
            ->addColumn('googleplaceid', function (Venue $venue) {
                return '<a href="https://www.google.com/maps/place/?q=place_id:' . $venue->googleplaceid . '" target="_blank" class="btn btn-sm btn-primary mb-1"><i class="bi bi-link"></i></a>';
            })
            ->addColumn('status', function (Venue $venue) {
                $statusLabel = $venue->status; // Assuming you have a label method

                switch ($venue->status) {
                    case \App\Enums\VenueStatusEnum::pending:
                        return '<span class="badge badge-primary">' . $statusLabel . '</span>';
                    case \App\Enums\VenueStatusEnum::online:
                        return '<span class="badge badge-success">' . $statusLabel . '</span>';
                    case \App\Enums\VenueStatusEnum::offline:
                        return '<span class="badge badge-danger">' . $statusLabel . '</span>';
                    case \App\Enums\VenueStatusEnum::canceled:
                        return '<span class="badge badge-warning">' . $statusLabel . '</span>';
                    default:
                        return '<span class="badge badge-dark">' . $statusLabel . '</span>';
                }
            })
            ->rawColumns(['action', 'venue', 'googleplaceid', 'status']) // Specify the columns that should not be escaped
            ->setRowId('id');

        // Add additional columns if the user is an admin
        if (auth()->user()->role == 'admin') {
            $dataTable->addColumn('user_name', function (Venue $venue) {
                $url = route('users.show', $venue->user);
                return '<a href="' . $url . '">' .$venue->user->name. '</a>'; // Handle case where user might be null
            })
            ->rawColumns(['action', 'user_name', 'venue', 'googleplaceid', 'status']);
        }

        return $dataTable;
    }


    /**
     * Get the query source of dataTable.
     */
    public function query(Venue $model): QueryBuilder
    {
        if(auth()->user()->role == 'admin'){
            $query = $model->newQuery()->with('user'); // Include the user relationship for admins
        } else {
            $query = $model->newQuery()->where('user_id', auth()->user()->id);
        }

        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('venues-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->orderBy(1)
                    ->selectStyleSingle()
                    ->scrollX(true)
                    ->scrollCollapse(true) // Allow the table to resize when scrolling
                    ->responsive(true) // Ensure table is responsive
                    ->buttons([
                        Button::make('add'),
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
        $columns = [
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(100)
                ->addClass('text-center'),
            Column::make('venue')->title('Venue'),
            Column::make('googleplaceid')->title('Google Place URL'),
            Column::computed('status')
                ->exportable(true)
                ->printable(true)
                ->width(100)
                ->addClass('text-center'),
        ];

        if(auth()->user()->role == 'admin') {
            array_splice($columns, 1, 0, [
                Column::make('user_name')->title('User Name'),
            ]);
        }

        return $columns;
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Venues_' . date('YmdHis');
    }
}
