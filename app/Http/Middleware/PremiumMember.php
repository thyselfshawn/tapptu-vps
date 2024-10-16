<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Enums\PackageNameEnum;

class PremiumMember
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Allow if user is admin
        if ($user->role === 'admin') {
            return $next($request);
        }

        // Check if the user is a venue and has an active "premium" subscription
        if ($user->role === 'venue') {
            $hasActivePremiumMembership = $user->venues()
                ->whereHas('subscriptions', function ($query) {
                    $query->where('status', true) // Active subscriptions only
                        ->whereHas('package', function ($q) {
                            $q->where('name', PackageNameEnum::premium->value);
                        });
                })->exists();

            if ($hasActivePremiumMembership) {
                return $next($request);
            }
        }

        return abort(403, 'No active premium subscription found or unauthorized.');
    }
}
