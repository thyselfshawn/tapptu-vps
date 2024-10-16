<?php

use App\Models\Venue;
use App\Models\Membership;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Subscription;
use Stripe\Checkout\Session as CheckoutSession;

class BillingController extends Controller
{
    public function __construct()
    {
        // Set your Stripe secret key
        Stripe::setApiKey(env('STRIPE_SECRET'));
    }

    /**
     * Show the subscription page for the venue.
     */
    public function showSubscriptionPage(Venue $venue)
    {
        $currentMembership = $venue->currentMembership();
        return view('billing.subscription', compact('venue', 'currentMembership'));
    }

    /**
     * Create or update the Stripe subscription for the venue.
     */
    public function createOrUpdateSubscription(Request $request, Venue $venue)
    {
        // Calculate the total charge based on the venue's cards
        $totalCharge = $this->calculateTotalCharge($venue);

        // Ensure Stripe customer exists
        $this->createStripeCustomerForVenue($venue);

        // Get the current membership of the venue
        $membership = $venue->currentMembership();

        if ($membership->stripe_subscription_id) {
            // Update existing subscription
            $this->updateSubscription($membership, $totalCharge);
        } else {
            // Create a new subscription
            $this->createSubscription($venue, $membership, $totalCharge);
        }

        return redirect()->route('billing.subscription', $venue->id)->with('success', 'Subscription updated successfully.');
    }

    /**
     * Checkout process for venue subscription.
     */
    public function checkout(Request $request, Venue $venue)
    {
        // Calculate the total charge and initiate a Stripe checkout session
        $totalCharge = $this->calculateTotalCharge($venue);

        // Create Stripe Checkout Session
        $checkoutSession = CheckoutSession::create([
            'customer' => $venue->stripe_customer_id,
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price' => $venue->currentMembership()->package->stripe_price_id,
                    'quantity' => $totalCharge,
                ],
            ],
            'mode' => 'subscription',
            'success_url' => route('billing.success', ['venue' => $venue->id]),
            'cancel_url' => route('billing.cancel', ['venue' => $venue->id]),
        ]);

        return redirect($checkoutSession->url);
    }

    /**
     * Handle successful subscription.
     */
    public function subscriptionSuccess(Request $request, Venue $venue)
    {
        // Optionally handle post-payment logic here, such as updating membership status
        return redirect()->route('billing.subscription', $venue->id)->with('success', 'Subscription successful!');
    }

    /**
     * Handle canceled subscription.
     */
    public function subscriptionCancel(Request $request, Venue $venue)
    {
        // Optionally handle cancellation logic here
        return redirect()->route('billing.subscription', $venue->id)->with('error', 'Subscription was canceled.');
    }

    /**
     * Calculate total charge based on venue's active cards.
     */
    private function calculateTotalCharge(Venue $venue)
    {
        $membership = $venue->currentMembership();
        if (!$membership) return 0;

        $package = $membership->package;
        $activeCards = $venue->rfids->where('status', 'paid');

        $total = 0;
        foreach ($activeCards as $index => $card) {
            $total += $index === 0 ? $package->first_price : $package->second_price;
        }

        return $total;
    }

    /**
     * Create a Stripe customer for the venue if it doesn't exist.
     */
    private function createStripeCustomerForVenue(Venue $venue)
    {
        if (!$venue->stripe_customer_id) {
            $customer = \Stripe\Customer::create([
                'name' => $venue->name,
                'email' => $venue->user->email, // Assuming venue has an associated user with email
            ]);

            $venue->stripe_customer_id = $customer->id;
            $venue->save();
        }
    }

    /**
     * Create a new Stripe subscription for the venue.
     */
    private function createSubscription(Venue $venue, Membership $membership, $totalCharge)
    {
        $subscription = Subscription::create([
            'customer' => $venue->stripe_customer_id,
            'items' => [
                [
                    'price' => $membership->package->stripe_price_id,
                    'quantity' => $totalCharge,
                ],
            ],
        ]);

        $membership->stripe_subscription_id = $subscription->id;
        $membership->save();
    }

    /**
     * Update an existing Stripe subscription.
     */
    private function updateSubscription(Membership $membership, $totalCharge)
    {
        Subscription::update($membership->stripe_subscription_id, [
            'items' => [
                [
                    'price' => $membership->package->stripe_price_id,
                    'quantity' => $totalCharge,
                ],
            ],
        ]);
    }
}

