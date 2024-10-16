<?php

namespace App\DataTables;

use App\Models\Review;
use App\Models\Venue;
use App\Models\Card;
use App\Enums\ReviewTypeEnum;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cookie;

class GooglesDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('status', function ($review) {
                return ucfirst(str_replace('_', ' ', $review->status->value)); // Format the enum to display string
            })
            ->editColumn('created_at', function ($review) {
                return $review->created_at->format('Y-m-d'); // Convert created_at to human-readable format
            })
            ->addColumn('venue_id', function ($review) {
                return $review->venue ? $review->venue->name : 'N/A'; // Safely handle null venue
            })
            ->addColumn('card_id', function ($review) {
                return $review->card ? $review->card->name : 'N/A'; // Safely handle null card
            })
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Review $model): QueryBuilder
    {
        $query = $model->newQuery()
            ->with(['card', 'venue']) // Eager load card and venue relationships
            ->select(['reviews.*']);

        // Check the user's role
        if (auth()->user()->role == 'admin') {
            // Admin role: retrieve all reviews
            return $query;
        } else {
            // Non-admin role: retrieve only the reviews associated with the user's venues
            return $query
                ->join('venues', 'reviews.venue_id', '=', 'venues.id') // Join the venues table
                ->where('venues.user_id', auth()->user()->id) // Filter by the authenticated user's ID
                ->orderBy('reviews.id', 'desc'); // Order by review ID
        }

        // Apply venue filter
        if ($this->request()->has('venue') && $this->request()->input('venue') != "all") {
            $venue = Venue::where('slug', $this->request()->input('venue'))->first();
            if ($venue) {
                $query->where('venue_id', $venue->id);
            }
        }

        // Apply card filter
        if ($this->request()->has('card') && $this->request()->input('card') != "all") {
            $card = Card::where('uuid', $this->request()->input('card'))->first();
            if ($card) {
                $query->where('card_id', $card->id);
            }
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
            ->setTableId('reviews-table')
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
                Button::make('reload'),
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('id'),
            Column::make('name'),
            Column::make('phone'),
            Column::make('venue_id')->title('Venue'),
            Column::make('card_id')->title('Card'),
            Column::make('message'),
            Column::make('created_at'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Reviews_' . date('YmdHis');
    }
}
