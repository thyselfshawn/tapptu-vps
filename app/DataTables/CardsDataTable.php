<?php

namespace App\DataTables;

use App\Models\Card;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class CardsDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', function (Card $card) {
                return view('cards.action', ['item' => $card]);
            })
            ->editColumn('name', function (Card $card) {
                return $card->created_at->format('Y-m-d'); // Convert created_at to human-readable format
            })
            ->setRowId('id');
    }


    /**
     * Get the query source of dataTable.
     */
    public function query(Card $model): QueryBuilder
    {
        // Check the user's role
        if (auth()->user()->role == 'admin') {
            // Admin role: retrieve all cards
            return $model->newQuery()->orderBy('id', 'desc');
        } else {
            // Non-admin role: retrieve only the cards associated with the user's venues
            return $model->newQuery()
                ->select('cards.*')
                ->distinct()
                ->join('venue_cards', 'cards.id', '=', 'venue_cards.card_id')
                ->join('venues', 'venue_cards.venue_id', '=', 'venues.id')
                ->where('venues.user_id', auth()->user()->id)
                ->orderBy('cards.id', 'desc');
        }
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('cards-table')
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
                ->width(150)
                ->addClass('text-center'),
            Column::make('name'),
            Column::make('status'),
            Column::make('token'),
        ];

        // Add 'id' column only for admin users
        if (auth()->user()->role == 'admin') {
            array_splice($columns, 1, 0, [Column::make('id')]);
        }

        return $columns;
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Cards_' . date('YmdHis');
    }
}
