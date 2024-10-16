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
    public function show_qr($id)
    {
        $rfid = Card::findOrFail($id);
        if ($rfid) {
            $qrCode = new QrCode(config('app.url') . '/scan-qr?card=' . $rfid->uuid);
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
    
    public function scan_qr(Request $request)
    {
        $rfid = $request->query('card');
        $rfid = Card::where('uuid', $rfid)->first();
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
