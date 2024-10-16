<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Membership;
use Symfony\Component\HttpFoundation\Response;

class CheckMembership
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        $currentDate = Carbon::now();
        
        // Check if the user is authenticated
        if ($user && $user->role == 'venue') {
            // Check if the user has an active membership
            $activeMembership = Membership::where('user_id', $user->id)
                ->where('end_at', '>=', $currentDate)
                ->first();
            // If no active membership, redirect to memberships.index
            if (!$activeMembership) {
                return redirect()->route('memberships.index');
            }
        }

        return $next($request);
    }
}
