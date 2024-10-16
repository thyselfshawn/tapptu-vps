<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Enums\PackageNameEnum;

class StandardMember
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

        // Check if the user is a venue and has an active "standard" subscription
        if ($user->role === 'venue') {
            $hasActiveStandardMembership = $user->venues()
                ->whereHas('subscriptions', function ($query) {
                    $query->where('status', true) // Active subscriptions only
                        ->whereHas('package', function ($q) {
                            $q->whereIn('name', [PackageNameEnum::standard->value, PackageNameEnum::premium->value]);
                        });
                })->exists();

            if ($hasActiveStandardMembership) {
                return $next($request);
            }
        }

        return abort(403, 'No active standard membership found or unauthorized.');
    }
}
