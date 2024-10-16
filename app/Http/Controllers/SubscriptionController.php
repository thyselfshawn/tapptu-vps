<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\Package;
use App\Models\Venue;
use Illuminate\Http\Request;
use App\DataTables\SubscriptionDataTable;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    public function __construct()
    {
        // Only admins can access
        $this->middleware('admin')->only(['index', 'destroy']);

        // Admins and the venue themselves can access
        $this->middleware('venue')->only(['subscribe']);
    }
    // Display a listing of the subscriptions
    public function index(SubscriptionDataTable $dataTable)
    {
        return $dataTable->render('subscriptions.index');
    }

    // Subcribe the specified subscription for user
    public function subscribe(Request $request, Venue $venue)
    {
        $user = auth()->user(); // Get the authenticated user
        $currentDate = Carbon::now(); // Current date and time

        // Check if the user has any active subscription
        $activesubscription = Subscription::where('venue_id', $venue->id)
            ->where('end_at', '=>', $currentDate)
            ->first();
        $package = Package::findOrFail($request->package);
        // If the user already has an active subscription
        if ($activesubscription) {
            // $additionalDays = $package->type == 'month' ? 30 : 365;
            $newEndDate = $activesubscription->end_at->addDays(30);
            $activesubscription->update([
                'end_at' => $newEndDate,
            ]);
            return redirect()->route('subscriptions.index')->with('success', 'Subscribed successfully.');
        }

        // If no active subscription, create a new one
        $end_at = $package->type == 'month' ? 30 : 365;

        Subscription::create([
            'venue_id' => $user->id,
            'package_id' => $package->id, // or the package ID from request or another logic
            'end_at' => $currentDate->addDays($end_at), // Set the end date for 30 days trial or any logic
        ]);

        return redirect()->route('subscriptions.index')->with('success', 'Subscribed successfully.');
    }

    // Remove the specified subscription from storage
    public function destroy(Subscription $subscription)
    {
        $subscription->delete();

        return redirect()->route('subscriptions.index')->with('success', 'subscription deleted successfully.');
    }
}
