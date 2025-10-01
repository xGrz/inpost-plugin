<?php

namespace Xgrz\InPost\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IpAddressRestrictionMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (app()->environment('local')) {
            return $next($request);
        }

        $allowedSubnet = config('inpost.webhook_ip_restriction', '127.0.0.0/24');
        $ip = $request->header('CF-Connecting-IP') ?? $request->ip();

        if (! $this->ipInSubnet($ip, $allowedSubnet)) {
            abort(404);
        }

        return $next($request);
    }


    private function ipInSubnet(string $ip, string $subnet): bool
    {
        [$subnetIp, $mask] = explode('/', $subnet);

        $ipLong = ip2long($ip);
        $subnetLong = ip2long($subnetIp);
        $mask = ~((1 << (32 - $mask)) - 1);

        return ($ipLong & $mask) === ($subnetLong & $mask);
    }
}
