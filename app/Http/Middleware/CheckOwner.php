<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckOwner
{
    public function handle(Request $request, Closure $next)
    {
        // Iterate through all route parameters to find the model
        $model = null;
        foreach ($request->route()->parameters() as $parameter) {
            if (is_object($parameter) && method_exists($parameter, 'getKey')) {
                $model = $parameter;
                break;
            }
        }

        $currentUser = auth()->user();

        // Check if the current user is an admin or the owner of the model
        if ($currentUser && ($currentUser->role === 'admin' || ($model && $currentUser->id === $model->user_id))) {
            return $next($request);
        }

        return redirect()->route('home')->with('error', 'Unauthorized access.');
    }
}
