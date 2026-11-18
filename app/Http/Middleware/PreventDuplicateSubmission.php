<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class PreventDuplicateSubmission
{
    public function handle(Request $request, Closure $next): Response
    {
        $validated = $request->validate([
            '_submission_token' => ['required', 'uuid'],
        ]);

        if (! Cache::add('form-submission:'.$validated['_submission_token'], true, now()->addMinutes(10))) {
            return back()->withErrors([
                'submission' => 'This form has already been submitted.',
            ]);
        }

        return $next($request);
    }
}
