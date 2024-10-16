<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Models\Contact;
use App\Models\Card;
use App\Models\Review;
use App\Models\Venue;
use App\Models\Voucher;
use App\Models\Tap;
use App\Enums\VoucherStatusEnum;
use App\Enums\CardStatusEnum;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class GuestController extends Controller
{    
    public function qr_card($card)
    {
        $card = Card::where('uuid', $card)->first();
        if ($card) {
            if($card->status == CardStatusEnum::pending){
                return $this->generateQrResponse(route('guest.check_card', ['card' => $card->uuid]));
            }else if($card->status == CardStatusEnum::attached){
                return $this->generateQrResponse(route('guest.view_card', ['venue' => $card->firstVenue()->slug, 'card' => $card->uuid]));
            }
        }
        return response('RFID card not found, unable to show qr!', 404);
    }

    public function check_card($card)
    {
        $card = Card::where('uuid', $card)->first();
        if($card){
            if($card->status == CardStatusEnum::pending){
                // save for 
                session(['setup-card' => $card->uuid]);
                return view('guest.setup_card', compact('card'));
            }else if($card->status == CardStatusEnum::attached){
                if(auth()->user() && auth()->user()->id == $card->firstVenue()->user_id){
                    return redirect()->route('venues.edit', ['venue' => $card->firstVenue()->slug]);
                }
                $venue = $card->firstVenue();
                return redirect()->route('guest.view_card', ['venue' => $venue->slug, 'card' => $card->uuid]);
            }else{
                return redirect()->route('guest.view_card', ['venue' => $card->firstVenue()->slug, 'card' => $card->uuid]);
            }
        }
        return response('CARD not found, check failed!', 404);
    }
    // guest card urls
    public function view_card($venue, $card)
    {
        $card = Card::where('uuid', $card)->first();
        if ($card && ($card->status == CardStatusEnum::pending || $card->status == CardStatusEnum::attached)) {
            $venue = $card->firstVenue();
            $this->storeTap($card->id, $venue->id, 'venue_page');
            return view('guest.index_review', compact('venue', 'card'));
        }
        return view('guest.message', [
            'message' => 'Can not view the card!',
        ]);
    }

    // guest voucher urls
    public function view_voucher($venue, $uuid)
    {
        $venue = Venue::where('slug', $venue)->first();
        if($venue){
            $voucher = Voucher::where('uuid', $uuid)->first();
            $this->storeTap($voucher->card_id, $venue->id, 'voucher_claim');
            if($voucher->status == VoucherStatusEnum::pending){
                $this->claim_voucher($venue->slug, $voucher->uuid);
                // return view('guest.view_voucher', compact('venue','voucher'));
            }else if($voucher->status == VoucherStatusEnum::mistake){
                return view('guest.message', [
                    'message' => 'Reviewer pressed claim button!'
                ]);
            }else if($voucher->status == VoucherStatusEnum::claimed){
                return view('guest.message', [
                    'message' => 'Voucher already claimed!'
                ]);
            }
            return view('guest.message', [
                'message' => 'No voucher to claim!',
            ]);
        }
        return view('guest.message', [
            'message' => 'No venue found!',
        ]);
    }

    public function claim_voucher($venue, $uuid)
    {
        $venue = Venue::where('slug', $venue)->first();
        if($venue){
            $voucher = Voucher::where('uuid', $uuid)->where('venue_id', $venue->id)->first();
            if($voucher && $voucher->status == VoucherStatusEnum::pending){
                if(auth()->user() && auth()->user()->id == $voucher->venue->user->id){
                    $voucher->update(['status' => VoucherStatusEnum::claimed]);
                    return view('guest.message', [
                        'message' => 'Voucher claimed!',
                    ]);
                }
                $voucher->update(['status' => VoucherStatusEnum::mistake]);
                return view('guest.message', [
                    'message' => 'No voucher to claim!',
                ]);
            }
            return view('guest.message', [
                'message' => 'No voucher to claim!',
            ]);
        }
        return view('guest.message', [
            'message' => 'No venue found!',
        ]);
        
    }

    public function qr_voucher($venue, $uuid)
    {
        $venue = Venue::where('slug', $venue)->first();
        $voucher = Voucher::where('uuid', $uuid)->first();
        if ($voucher) {
            
            return $this->generateQrResponse(route('guest.view_voucher', ['venue' => $venue->slug, 'uuid' => $voucher->uuid]));
        }
        return view('guest.message', [
            'message' => 'Venue or Voucher not found!',
        ]);
    }

    // visitor voucher create
    public function create_voucher($venue, $card, $type)
    {
        $type = $type;
        
        $venue = Venue::where('slug', $venue)->first();
        $card = Card::where('uuid', $card)->first();

        if(session('voucher-contact')){
            return redirect()->route('guest.create_review', ['venue' => $venue->slug, 'card' => $card->uuid]);
        }
        session(['review-type' => $type]);
        $this->storeTap($card->id, $venue->id, $type);
        return view('guest.create_voucher', compact('venue', 'card'));
    }

    // visitor voucher store
    public function store_voucher(Request $request, $venue, $card)
    {
        $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string'
        ]);
        $instanceId = '16821';
        $validWhatsApp = $this->validate_whatsapp($instanceId, $request->input('phone'));
        if(!$validWhatsApp){
            return redirect()->back()->with('error', 'Invalid whatsapp number!');
        }
        $venue = Venue::where('slug', $venue)->first();
        $card = Card::where('uuid', $card)->first();
        $contact = Contact::create([
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'uuid' => str()->uuid(),
        ]);
        if($contact){
            session(['voucher-contact' => $contact->id]);
            $voucher = Voucher::create([
                'text' => $venue->voucher,
                'uuid' => str()->uuid(),
                'contact_id' => $contact->id,
                'card_id' => $card->id,
                'venue_id' => $venue->id,
            ]);
            if($voucher){
                $sent = $this->sendWhatsAppMessage($contact->phone, route('guest.qr_voucher', ['venue' => $venue->slug, 'uuid' => $voucher->uuid]));
                if($sent){
                    session(['voucher-message' => $sent]);
                    session(['voucher-uuid' => $voucher->uuid]);
                    $this->storeTap($card->id, $venue->id, 'voucher_sent');
                }
            }
            
            if(session('review-type') === 'google_feedback'){
                $url = 'https://search.google.com/local/writereview?placeid=' . $venue->googleplaceid;
                return redirect()->away($url);
            }
            return redirect()->route('guest.create_review', ['venue' => $venue->slug, 'card' => $card->uuid]);        
        }
    }

    // visitor honest review create
    public function create_review($venue, $card)
    {
        $venue = Venue::where('slug', $venue)->first();
        $card = Card::where('uuid', $card)->first();
        return view('guest.create_review', compact('venue', 'card'));
    }

    // visitor honest review store
    public function store_review(Request $request, $venue, $card)
    {
        $type = session('review-type');
        $venue = Venue::where('slug', $venue)->first();
        $card = Card::where('uuid', $card)->first();
        if($venue->slug){
            if(session('voucher-contact')){
                $contact = Contact::findOrFail(session('voucher-contact'));
                $name = $contact->name;
                $phone = $contact->phone;
                session()->forget('voucher-contact');
            }else{
                $name = $request->input('name');
                $phone = $request->input('phone');
            }
            $reviewExist = Review::where('phone', $phone)->where('card_id', $card->id)->exists();
            if(!$reviewExist){
                $review = Review::create([
                    'name' => $name,
                    'phone' => $phone,
                    'type' => $type,
                    'venue_id' => $venue->id,
                    'card_id' => $card->id,
                    'message'=> $request->input('message')
                ]);
                if($review){
                    if(session('voucher-message')){
                        $voucher = session('voucher-uuid');
                        session()->forget('voucher-message');
                        session()->forget('voucher-uuid');
                        return redirect()->route('guest.qr_voucher', ['venue' => $venue->slug, 'uuid' => $voucher]);
                    }
                    return view('guest.message', [
                        'message' => 'Review created created!',
                    ]); 

                }
                return view('guest.message', [
                    'message' => 'Failed to create review!',
                ]);
            }
            return view('guest.message', [
                'message' => 'You have already reviewed this card!',
            ]);            
        }
        return view('guest.message', [
            'message' => 'Venue not found!',
        ]);
    }

    // generate a qr response
    protected function generateQrResponse($url)
    {
        $qrCode = new QrCode($url);
        // You can customize the QR code here (size, margin, encoding, etc.)
        $qrCode->setSize(300);
        $qrCode->setMargin(10);

        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        // Return the QR code as an image response
        return Response::make($result->getString(), 200, [
            'Content-Type' => $result->getMimeType(),
            'Content-Disposition' => 'inline; filename="qr-code.png"'
        ]);
    }

    protected function validate_whatsapp($instanceId, $phone)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://waapi.app/api/v1/instances/" . $instanceId . "/client/action/is-registered-user",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode([
                'contactId' => $phone . '@c.us'
            ]),
            CURLOPT_HTTPHEADER => [
                "accept: application/json",
                "authorization: Bearer cYksPNdqIYLktQKVokO52E0RgXQ6Z9V9Fjmp12uG23f49385",
                "content-type: application/json"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        $response_data = json_decode($response, true);
        if ($response_data['data']['status'] == 'success') {
            return $response_data['data']['data']['isRegisteredUser'];
        }
        return false;
    }

    protected function sendWhatsAppMessage($number, $message)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
        CURLOPT_URL => "https://waapi.app/api/v1/instances/16821/client/action/send-message",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode([
            'chatId' => $number . '@c.us',
            'message' => $message
        ]),
        CURLOPT_HTTPHEADER => [
            "accept: application/json",
            "authorization: Bearer cYksPNdqIYLktQKVokO52E0RgXQ6Z9V9Fjmp12uG23f49385",
            "content-type: application/json"
        ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return false;
        } else {
            return true;
        }
    }

    protected function storeTap($card, $venue, $type)
    {
        Tap::create([
            'card_id' => $card,
            'venue_id' => $venue,
            'type' => $type,
        ]);
    }
}
