<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Card;
use App\Models\Venue;
use App\Models\Review;
use App\Models\Tap;
use Carbon\Carbon;
use App\Enums\TapTypeEnum;
use Illuminate\Support\Facades\Cookie;
use App\DataTables\TapsDataTable;
use App\DataTables\ReviewsDataTable;

class ReportController extends Controller
{
    public function tap_report(TapsDataTable $dataTable, Request $request)
    {
        // Set default range to 7 days if no range is provided
        $tapRange = $request->has('range') ? $request->input('range') : '7';
        Cookie::queue('tap-range', $tapRange, 60); // Store the range for 1 hour

        // Build the query for calculating statistics
        if(auth()->user()->role == 'admin'){
            view()->share('venues', Venue::all());
            view()->share('cards', Card::all());
            $query = Tap::query();
        } else {
            view()->share('venues', auth()->user()->venues());
            view()->share('cards', Card::select('cards.*')
                ->distinct()
                ->join('venue_cards', 'cards.id', '=', 'venue_cards.card_id')
                ->join('venues', 'venue_cards.venue_id', '=', 'venues.id')
                ->where('venues.user_id', auth()->user()->id)
                ->orderBy('created_at', 'desc')
                ->get());

            $query = Tap::whereHas('venue', function ($query) {
                $query->where('user_id', auth()->user()->id);
            });
        }

        // Apply filters
        if ($request->has('venue') && $request->input('venue') != "all") {
            $venue = Venue::where(['slug' => $request->input('venue')])->first();
            $query->where('venue_id', $venue->id);
        }

        if ($request->has('card') && $request->input('card') != "all") {
            $card = Card::where(['uuid' => $request->input('card')])->first();
            $query->where('card_id', $card->id);
        }

        if ($request->has('review') && $request->input('review') != "all") {
            $query->where('type', $request->input('review'));
        }

        if ($tapRange != "all") {
            $days = (int) $tapRange;
            $query->whereDate('created_at', '>=', Carbon::now()->subDays($days));
        }

        // Calculate statistics
        $statistics = [
            'venuePageLoads' => $query->clone()->where('type', TapTypeEnum::venue_page)->count(),
            'okFeedback' => $query->clone()->where('type', TapTypeEnum::good_feedback)->count(),
            'notOkFeedback' => $query->clone()->where('type', TapTypeEnum::bad_feedback)->count(),
            'voucherSent' => $query->clone()->where('type', TapTypeEnum::voucher_sent)->count(),
            'googleReviewClicks' => $query->clone()->where('type', TapTypeEnum::google_feedback)->count(),
            'voucherClaims' => $query->clone()->where('type', TapTypeEnum::voucher_claim)->count(),
        ];

        // Share statistics with the view
        view()->share('statistics', $statistics);

        return $dataTable->render('reports.tap');
    }

    public function review_report(ReviewsDataTable $dataTable, Request $request)
    {
        // Build the query for calculating statistics
        if(auth()->user()->role == 'admin'){
            view()->share('venues', Venue::all());
            view()->share('cards', Card::all());
            $query = Review::query();
        } else {
            view()->share('venues', auth()->user()->venues());
            view()->share('cards', Card::select('cards.*')
                ->distinct()
                ->join('venue_cards', 'cards.id', '=', 'venue_cards.card_id')
                ->join('venues', 'venue_cards.venue_id', '=', 'venues.id')
                ->where('venues.user_id', auth()->user()->id)
                ->orderBy('created_at', 'desc')
                ->get());

            $query = Review::whereHas('venue', function ($query) {
                $query->where('user_id', auth()->user()->id);
            });
        }

        // Apply filters
        if ($request->has('venue') && $request->input('venue') != "all") {
            $venue = Venue::where(['slug' => $request->input('venue')])->first();
            $query->where('venue_id', $venue->id);
        }

        if ($request->has('card') && $request->input('card') != "all") {
            $card = Card::where(['uuid' => $request->input('card')])->first();
            $query->where('card_id', $card->id);
        }

        if ($request->has('review') && $request->input('review') != "all") {
            $query->where('type', $request->input('review'));
        }

        if ($request->has('range') && $request->input('range') != "all") {
            $days = (int) $request->input('range');
            $query->whereDate('created_at', '>=', Carbon::now()->subDays($days));
        }

        // Calculate statistics
        $statistics = [
            'okFeedback' => $query->clone()->where('type', TapTypeEnum::good_feedback)->count(),
            'notOkFeedback' => $query->clone()->where('type', TapTypeEnum::bad_feedback)->count(),
        ];

        // Share statistics with the view
        view()->share('statistics', $statistics);

        return $dataTable->render('reports.review');
    }

    public function google_report(Request $request)
    {
        // Set default range to 7 days if no range is provided
        $googleRange = $request->has('range') ? $request->input('range') : '7';

        // Build the query for calculating statistics
        if(auth()->user()->role == 'admin'){
            view()->share('venues', Venue::all());
            view()->share('cards', Card::all());
            $query = Review::query();
        } else {
            view()->share('venues', auth()->user()->venues());
            view()->share('cards', Card::select('cards.*')
                ->distinct()
                ->join('venue_cards', 'cards.id', '=', 'venue_cards.card_id')
                ->join('venues', 'venue_cards.venue_id', '=', 'venues.id')
                ->where('venues.user_id', auth()->user()->id)
                ->orderBy('created_at', 'desc')
                ->get());

            $query = Review::whereHas('venue', function ($query) {
                $query->where('user_id', auth()->user()->id);
            });
        }

        // Apply filters
        if ($request->has('venue') && $request->input('venue') != "all") {
            $venue = Venue::where(['slug' => $request->input('venue')])->first();
            $query->where('venue_id', $venue->id);
        }

        if ($request->has('card') && $request->input('card') != "all") {
            $card = Card::where(['uuid' => $request->input('card')])->first();
            $query->where('card_id', $card->id);
        }

        if ($googleRange != "all") {
            $days = (int) $googleRange;
            $query->whereDate('created_at', '>=', Carbon::now()->subDays($days));
        }

        // Calculate statistics
        $statistics = [
            'googleFeedback' => $query->clone()->where('type', TapTypeEnum::google_feedback)->count(),
            'startingFeedback' => 0,
        ];

        return view('reports.google', compact('statistics'));
    }

    public function tap_filter(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'range' => 'nullable|in:1,7,30,all',
            'venue' => 'nullable|string',
            'card' => 'nullable|string',
            'review' => 'nullable|string',
        ]);

        // Store the selected range in a cookie
        Cookie::queue('tap-range', $request->input('range', '7'), 60); // Store the range for 1 hour

        // Forward the request to tap_report for rendering
        return $this->tap_report(new TapsDataTable(), $request);
    }

    public function review_filter(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'range' => 'nullable|in:1,7,30,all',
            'venue' => 'nullable|string',
            'card' => 'nullable|string',
            'review' => 'nullable|string',
        ]);

        // Forward the request to tap_report for rendering
        return $this->review_report(new ReviewsDataTable(), $request);
    }

    public function google_filter(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'range' => 'nullable|in:1,7,30,all',
            'venue' => 'nullable|string',
            'card' => 'nullable|string',
        ]);

        // Store the selected range in a cookie
        Cookie::queue('google-range', $request->input('range', '7'), 60); // Store the range for 1 hour

        // Forward the request to tap_report for rendering
        return $this->google_report(new GooglesDataTable(), $request);
    }

}
