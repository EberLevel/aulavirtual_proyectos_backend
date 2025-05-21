<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;
use App\Models\Domains;

class ValidateDomainMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $domain = $request->route('domain');

        if (!$domain) {
            return response()->json(['error' => 'Domain not provided'], 400);
        }

        $cacheKey = "domain_id_{$domain}";

        if (Cache::has($cacheKey)) {
            $domainId = Cache::get($cacheKey);
        } else {

            $domainRecord = Domains::where('nombre', $domain)->first();

            if (!$domainRecord) {
                return response()->json([
                    'status' => false,
                    'error' => 'Invalid domain'
                ], 404);
            }

            $domainId = $domainRecord->id;


            Cache::put($cacheKey, $domainId, 60); // Cache for 60 minutes
        }

        // Share the domain ID with the request
        $request->attributes->set('domain_id', $domainId);

        return $next($request);
    }
}
