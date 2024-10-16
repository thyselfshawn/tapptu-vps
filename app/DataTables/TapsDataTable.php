<?php

namespace App\DataTables;

use App\Models\Tap;
use App\Models\Venue;
use App\Models\Card;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Carbon\Carbon;

class TapsDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('created_at', function ($tap) {
                return $tap->created_at->format('Y-m-d'); // Convert created_at to human-readable format
            })
            ->editColumn('type', function ($tap) {
                // Format the tap type value for display
                return ucfirst(str_replace('_', ' ', $tap->type->value));
            })
            ->addColumn('card_id', function ($tap) {
                return $tap->card ? $tap->card->name : 'N/A'; // Safely handle null card
            })
            ->addColumn('venue_id', function ($tap) {
                return $tap->venue ? $tap->venue->name : 'N/A'; // Safely handle null venue
            })
            ->setRowId('id');
    }


    /**
     * Get the query source of dataTable.
     */
    public function query(Tap $model): QueryBuilder
    {
        $query = $model->newQuery()
            ->with(['card', 'venue']) // Eager load card and venue relationships
            ->select(['taps.*']); // Select all columns from taps table

        // Apply venue filter
        if ($this->request()->has('venue') && $this->request()->input('venue') != "all") {
            $venue = Venue::where(['slug' => $this->request()->input('venue')])->first();
            if ($venue) {
                $query->where('venue_id', $venue->id);
            }
        }

        // Apply card filter
        if ($this->request()->has('card') && $this->request()->input('card') != "all") {
            $card = Card::where(['uuid' => $this->request()->input('card')])->first();
            if ($card) {
                $query->where('card_id', $card->id);
            }
        }

        // Apply review filter
        if ($this->request()->has('review') && $this->request()->input('review') != "all") {
            $query->where('type', $this->request()->input('review'));
        }

        // Apply date range filter
        if ($this->request()->has('range') && $this->request()->input('range') != "all") {
            $days = (int) $this->request()->input('range');
            $query->whereDate('created_at', '>=', Carbon::now()->subDays($days));
        }

        return $query->orderBy('created_at', 'desc');
    }


    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('taps-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->selectStyleSingle()
            ->scrollX(true)
            ->scrollCollapse(true) // Allow the table to resize when scrolling
            ->responsive(true); // Ensure table is responsive
    }


    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('card_id')->title('Card Name'),
            Column::make('venue_id')->title('Venue Name'),
            Column::make('type')->title('Review Type'),
            Column::make('created_at')->title('Created At'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Taps_' . date('YmdHis');
    }
}
