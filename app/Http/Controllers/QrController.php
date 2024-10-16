<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Review;
use App\Models\Venue;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class QrController extends Controller
{
    public function qr_card($card)
    {
        dd($card);
        $card = Card::where('uuid', $card)->first();
        dd(route('guest.check_card', ['card' => $card->uuid]));
        if ($card) {
            $qrCode = new QrCode(route('guest.check_card', ['card' => $card->uuid]));
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
        return response('RFID not found', 404);
    }
    
    public function check_card($card)
    {
        $rfid = Card::where('uuid', $card)->first();
        if ($rfid) {
            if($rfid->status == 'pending'){
                session(['setup-card' => $rfid->uuid]);
                return view('qr', compact('rfid'));
            }else if($rfid->status == 'setup'){
                $venue = $rfid->firstVenue();
                return view('reviews.review', compact('venue'));
            }
        }
        return response('RFID not found', 404);
    }

    public function take_feedback(Request $request, $slug)
    {
        $venue = Venue::where('slug', $slug)->first();
        if($venue->slug){
            $review = Review::create(['venue_id' => $venue->id, 'message'=> $request->input('message')]);
            if($review){
                dd('Lets to voucher qr page!');
                return view('');
            }
        }
    }
}
