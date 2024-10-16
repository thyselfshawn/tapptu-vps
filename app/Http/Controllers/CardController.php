<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use App\Models\Card;
use App\Http\Requests\StoreCardRequest;
use App\Http\Requests\UpdateCardRequest;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use ZipArchive;
use App\Enums\CardStatusEnum;
use App\DataTables\CardsDataTable;

class CardController extends Controller
{    
    public function __construct()
    {
        // Only admins can access
        $this->middleware('admin')->only(['create', 'store', 'show', 'edit', 'update', 'destroy', 'download']);
    }
    
    public function index(CardsDataTable $dataTable)
    {
        return $dataTable->render('cards.index');
    }

    public function store(StoreCardRequest $request)
    {
        $numberOfItems = $request->input('number');

        for ($i = 0; $i < $numberOfItems; $i++) {
            Card::create([
                'uuid' => str()->uuid(),
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        return redirect()->route('cards.index')->with('success', 'RFID card created successfully.');
    }

    public function show(Card $card)
    {
        return view('cards.show', compact('rfid'));
    }

    public function edit(Card $card)
    {
        return view('cards.edit', compact('card'));
    }

    public function update(UpdateCardRequest $request, Card $card)
    {
        $card->update($request->validated());
        return redirect()->route('cards.index')->with('success', 'RFID card updated successfully.');
    }

    public function destroy(Card $card)
    {
        $card->delete();
        return redirect()->route('cards.index')->with('success', 'RFID card deleted successfully.');
    }

    public function download(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'cardto' => 'required|integer',
            'cardfrom' => 'required|integer',
            'type' => 'required|string',
        ]);

        // Get the range of card IDs
        $cardTo = $request->input('cardto');
        $cardFrom = $request->input('cardfrom');
        $downloadType = $request->input('type');
        if ($downloadType == 'qr'){
            $filePath = $this->download_qr($cardFrom, $cardTo);
        }
        if ($downloadType == 'csv'){
            $filePath = $this->download_csv($cardFrom, $cardTo);
        }

        return response()->download($filePath)->deleteFileAfterSend(false);
    }

    protected function download_qr($from, $to)
    {
        // Define the directory where QR images will be stored in the public folder
        $downloadDir = public_path('downloads');
        // Clear the directory's contents
        if (File::exists($downloadDir)) {
            File::cleanDirectory($downloadDir); // Clears the directory
        }

        // Loop through the range and generate QR codes
        $writer = new PngWriter();
        for ($i = $from; $i <= $to; $i++) {
            $card = Card::findOrFail($i); // Find the card by ID
            // Generate the URL for the card
            if($card->status == CardStatusEnum::pending){
                $url = route('guest.qr_card', ['card' => $card->uuid]);
            }else if($card->status == CardStatusEnum::attached){
                $url = route('guest.view_card', ['venue' => $card->firstVenue()->slug, 'card' => $card->uuid]);
            }
            
            // Create the QR code
            $qrCode = new QrCode($url);
            $qrCode->setSize(300);
            $qrCode->setMargin(10);
            
            $result = $writer->write($qrCode);
            
            // Save the QR code image to the 'downloads' directory in public
            $filePath = $downloadDir . '/qr_code_' . $card->id . '.png';
            file_put_contents($filePath, $result->getString());
        }

        // Zip the QR code images
        $zipFileName = 'qr_codes_' . $to . '_to_' . $from . '.zip';
        $zipFilePath = public_path('downloads/' . $zipFileName);

        $zip = new ZipArchive();
        if ($zip->open($zipFilePath, ZipArchive::CREATE) === TRUE) {
            foreach (File::files($downloadDir) as $file) {
                // Only add the files that match the current range
                if (strpos($file->getFilename(), 'qr_') === 0) {
                    $zip->addFile($file->getPathname(), $file->getFilename());
                }
            }
            $zip->close();
        }
        return $zipFilePath;

    }

    protected function download_CSV($from, $to)
    {
        // Clear the directory's contents
        if (File::exists(public_path('downloads'))) {
            File::cleanDirectory(public_path('downloads')); // Clears the directory
        }
        // Define the file name and path for the CSV
        $fileName = 'cards_' . $from . '_to_' . $to . '.csv';
        $filePath = public_path('downloads/' . $fileName);

        // Open a file handle for writing
        $file = fopen($filePath, 'w');

        // Write the header row
        fputcsv($file, ['ID', 'Link']);

        // Loop through the range of card IDs and generate data
        for ($i = $from; $i <= $to; $i++) {
            $card = Card::findOrFail($i); // Find the card by ID
            
            // Generate the link based on the card's status
            $link = route('guest.qr_card', ['card' => $card->uuid]);

            // Write the data row to the CSV
            fputcsv($file, [$card->id, $link]);
        }

        // Close the file handle
        fclose($file);

        // Return the CSV file as a download
        return $filePath;
    }
}