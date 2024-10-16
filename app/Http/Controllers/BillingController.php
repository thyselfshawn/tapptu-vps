<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venue;
use App\Models\User;
use App\Models\Package;
use App\Enums\PackageNameEnum;
use App\Models\Card;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Setting;
use Xendit\Configuration;
use Xendit\Customer\CustomerApi;

class BillingController extends Controller
{
    /**
     * Create a subscription for a venue.
     */
    public function __construct()
    {
        Configuration::setXenditKey(Setting::first()->payment_secret);
    }
    public function createSubscription(Request $request)
    {
        $venue = Venue::where('slug', $request->venue)->first();
        $package = Package::findOrFail($request->package);

        if (!$venue || !$package) {
            return redirect()->back()->with('error', 'Invalid venue or package');
        }

        if ($venue->currentSubscription()->package->name == $package->name) {
            return redirect()->back()->with('error', "You're currently using this membership");
        }

        // xendit customer id
        $customer_id = $this->xenditFindOrCreateCustomer($venue);
        if ($venue->currentSubscription()->package->name == PackageNameEnum::standard->value && $package->name != PackageNameEnum::standard) {
            // user pay here
            // check if have plan then deactive it
            dd($venue->currentSubscription()->package->name);
            // then create new plan and redirect to payment
        }

        if ($venue->currentSubscription()->package->name == PackageNameEnum::premium->value && $package->name != PackageNameEnum::premium) {
            // user do not pay here
            if (empty($venue->currentSubscription()->xendit_plan_id)) {
                // create new xendit plan
                $url = $this->xenditCreatePlan($venue, $package, $customer_id);
                return redirect()->away($url);
            }
            // update cycle here
            dump('here 1');
            dump($venue->currentSubscription()->xendit_plan_id);
            dd($this->xenditGetPlan($venue->currentSubscription()->xendit_plan_id));

        }

    }

    /**
     * Add a card to an active venue subscription with proration.
     */
    public function addCardToSubscription(Request $request)
    {
        $venue = Venue::find($request->venue_id);

        if (!$venue) {
            return response()->json(['error' => 'Invalid venue'], 404);
        }

        $package = $venue->currentPackage();
        $subscription = Subscription::where('venue_id', $venue->id)
            ->where('status', 'active')
            ->first();

        if (!$subscription) {
            return response()->json(['error' => 'No active subscription found for the venue'], 404);
        }

        // Calculate proration for the remaining months
        $remainingMonths = Carbon::now()->diffInMonths($subscription->end_date);
        $secondPrice = $package->second_price;
        $proratedAmount = $secondPrice * ($remainingMonths / 12);

        // Add the card and create an invoice for the prorated payment
        DB::transaction(function () use ($venue, $proratedAmount, $request) {
            // Add the card to the venue
            Card::create([
                'venue_id' => $venue->id,
                'name' => $request->card_name,
            ]);

            // Create an invoice for the prorated amount
            $invoice = \Xendit\Invoice::create([
                'external_id' => 'venue_card_' . $venue->id . '_card_' . time(),
                'payer_email' => $venue->contact_email,
                'description' => 'Payment for additional card',
                'amount' => $proratedAmount,
                'success_redirect_url' => route('billing.success', ['venue_id' => $venue->id]),
                'failure_redirect_url' => route('billing.failed', ['venue_id' => $venue->id]),
            ]);

            // Update subscription amount for the next cycle
            $venue->subscription->update([
                'amount' => $venue->subscription->amount + $proratedAmount,
            ]);

            // Redirect user to Xendit payment page
            return redirect($invoice['invoice_url']);
        });
    }

    /**
     * Handle successful payment redirection.
     */
    public function paymentSuccess($venue_id)
    {
        $venue = Venue::find($venue_id);

        if (!$venue) {
            return redirect()->route('billing.failed', ['venue_id' => $venue_id]);
        }

        $subscription = Subscription::where('venue_id', $venue->id)
            ->first();

        if ($subscription) {
            $subscription->update([
                'status' => 'pending',
                'paid_at' => now(),
            ]);

            return view('billing.success', ['venue' => $venue]);
        }

        return redirect()->route('billing.failed', ['venue_id' => $venue->id]);
    }


    /**
     * Handle failed payment redirection.
     */
    public function paymentFailed($venue_id)
    {
        $venue = Venue::find($venue_id);

        return view('billing.failed', ['venue' => $venue]);
    }


    /**
     * Handle Xendit webhook for subscription updates.
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->all();

        if ($payload['event'] == 'invoice.paid') {
            // Handle successful payment
            $subscription = Subscription::where('xendit_plan_id', $payload['data']['subscription']['id'])->first();
            if ($subscription) {
                $subscription->update(['status' => 'paid']);
            }
        }

        if ($payload['event'] == 'subscription.paused' || $payload['event'] == 'subscription.stopped') {
            // Handle subscription cancellation or pause
            $subscription = Subscription::where('xendit_plan_id', $payload['data']['id'])->first();
            if ($subscription) {
                $subscription->update(['status' => 'inactive']);
            }
        }

        return response()->json(['message' => 'Webhook handled successfully'], 200);
    }

    private function xenditGetPlan($xendit_plan_id)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'GET https://api.xendit.co/recurring/plans/' . strval($xendit_plan_id),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'API-VERSION: 2020-10-31',
                'Content-Type: application/json',
                'Authorization: Basic ' . base64_encode($this->xenditSecretKey() . ':')
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response);

    }
    private function calculateTotal($venue, $package)
    {
        $firstPrice = $package->first_price;
        $secondPrice = $package->second_price;
        // Count the number of cards associated with the venue
        $cardCount = $venue->cards()->count();

        // Calculate the total price
        $total_price = $firstPrice; // First card always uses first_price

        // If there are additional cards, add their price
        if ($cardCount > 1) {
            $total_price += ($cardCount - 1) * $secondPrice;
        }
        return $total_price;
    }

    private function xenditSecretKey()
    {
        return Setting::first()->payment_secret;
    }

    private function xenditCreateCustomer(User $user)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.xendit.co/customers',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode([
                'reference_id' => strval($user->id),
                'mobile_number' => '+' . strval($user->phone),
                'email' => strval($user->email),
                'type' => 'INDIVIDUAL',
                'individual_detail' => [
                    'given_names' => strval($user->name)
                ]
            ]),
            CURLOPT_HTTPHEADER => array(
                'API-VERSION: 2020-10-31',
                'Content-Type: application/json',
                'Authorization: Basic ' . base64_encode($this->xenditSecretKey() . ':')
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $response = json_decode($response, true);
        auth()->user()->update(['customer_id' => $response['id'] ?? '']);
        return $response['id'] ?? '';
    }

    private function xenditFindOrCreateCustomer($venue)
    {
        // retrive xendit user
        $user = $venue->user;
        $reference_id = (string) $user->id;
        $customer_id = $user->customer_id;
        if (empty($customer_id)) {
            $customer_id = $this->xenditCreateCustomer($user);
        } else {
            try {
                $apiInstance = new CustomerApi();
                $response = $apiInstance->getCustomerByReferenceID($reference_id);
                if (!empty($response['data'][0])) {
                    $customer_id = $response['data'][0]['id'];
                }
            } catch (\Xendit\XenditSdkException $e) {
                \Log::error('Exception when calling CustomerApi->getCustomerByReferenceID: ' > $e->getMessage());
                \Log::error('Full Error: ' . json_encode($e->getFullError()));
            }
        }

        return $customer_id;
    }

    private function xenditCreatePlan($venue, $package, $customer_id)
    {
        $total_price = $this->calculateTotal($venue, $package);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.xendit.co/recurring/plans',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode([
                'reference_id' => strval($venue->id),
                'customer_id' => $customer_id,
                'recurring_action' => "PAYMENT",
                'currency' => "IDR",
                'amount' => $total_price * 100,
                'schedule' => [
                    'reference_id' => strval($package->id),
                    'interval' => strtoupper($package->type),
                    'interval_count' => 1,
                ],
                'notification_config' => [
                    'locale' => "en",
                    'recurring_created' => [
                        "WHATSAPP",
                        "EMAIL"
                    ],
                    'recurring_succeeded' => [
                        "WHATSAPP",
                        "EMAIL"
                    ],
                    'recurring_failed' => [
                        "WHATSAPP",
                        "EMAIL"
                    ]
                ],
                'failed_cycle_action' => "STOP",
                'immediate_action_type' => "FULL_AMOUNT",
                'metadata' => null,
                'description' => "Tapptu - " . ucfirst($venue->currentSubscription()->name) . ' - ' . ucfirst($venue->currentSubscription()->name) . ' ' . ucfirst($venue->currentSubscription()->type) . 'ly package.',
                'success_return_url' => route('venues.show', $venue->slug),
                'failure_return_url' => route('venues.show', $venue->slug),
            ]),
            CURLOPT_HTTPHEADER => array(
                'API-VERSION: 2020-10-31',
                'Content-Type: application/json',
                'Authorization: Basic ' . base64_encode($this->xenditSecretKey() . ':')
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $responseData = json_decode($response, true);
        // Store the subscription in your database
        $venue->currentSubscription()->update([
            'xendit_plan_id' => $responseData['id'],
            'amount' => $total_price,
            'end_at' => Carbon::now()->addMonths(strtoupper($package->type) == 'MONTH' ? 1 : 12),
        ]);
        return $responseData['actions'][0]['url'];
    }

    function xenditUpdatePlan($venue, $package)
    {
        $total_price = $this->calculateTotal($venue, $package);
        $curl = curl_init();

        $data = array(
            "currency" => "IDR",
            "amount" => $total_price * 100,
        );

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.xendit.co/recurring/plans/' . strval($venue->currentSubscription()->xendit_plan_id),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'PATCH',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'API-VERSION: 2020-10-31',
                'Content-Type: application/json',
                'update-scheduled-cycle: TRUE',
                'Authorization: Basic ' . base64_encode($this->xenditSecretKey() . ':')
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $responseData = json_decode($response, true);
        dd($responseData);
    }

    private function xenditDeactivatePlan(string $plan_id)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'POST https://api.xendit.co/recurring/plans/' . $plan_id . '/deactivate',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array(
                'API-VERSION: 2020-10-31',
                'Content-Type: application/json',
                'Authorization: Basic ' . base64_encode($this->xenditSecretKey() . ':')
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        dd($response);
    }

}
