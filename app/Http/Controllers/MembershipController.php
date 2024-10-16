<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use App\Models\Package;
use Illuminate\Http\Request;
use App\DataTables\MembershipsDataTable;

class MembershipController extends Controller
{
    public function __construct()
    {
        // Only admins can access
        $this->middleware('admin')->only(['index', 'destroy']);
        
        // Admins and the venue themselves can access
        $this->middleware('venue')->only(['subscribe']);
    }
    // Display a listing of the memberships
    public function index(MembershipsDataTable $dataTable)
    {
        return $dataTable->render('memberships.index');
    }

    // Subcribe the specified membership for user
    public function subscribe($id)
    {
        $user = auth()->user(); // Get the authenticated user
        $currentDate = Carbon::now(); // Current date and time

        // Check if the user has any active membership
        $activeMembership = Membership::where('user_id', $user->id)
            ->where('end_at', '=>', $currentDate)
            ->first();

        // If the user already has an active membership
        if ($activeMembership) {
            return redirect()->route('memberships.index')->withErrors('You already have an active membership.');
        }

        // If no active membership, create a new one
        // You'll need to define how the new membership is created. Assuming you have a default package to assign:

        $package = Package::findOrFail($id); // Change this to your default package ID or logic to select the package
        $end_at = $package->type == 'monthly' ? 30 : 365;

        Membership::create([
            'user_id' => $user->id,
            'package_id' => $package->id, // or the package ID from request or another logic
            'end_at' => $currentDate->addDays($end_at), // Set the end date for 30 days trial or any logic
        ]);

        return redirect()->route('memberships.index')->with('success', 'Membership subscribed successfully.');
    }

    // Remove the specified membership from storage
    public function destroy(Membership $membership)
    {
        $membership->delete();

        return redirect()->route('memberships.index')->with('success', 'Membership deleted successfully.');
    }
}
