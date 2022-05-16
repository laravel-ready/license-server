<?php

namespace LaravelReady\LicenseServer\Http\Middleware;

use Closure;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

use LaravelReady\LicenseServer\Support\DomainSupport;

class DomainGuardMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $host = $request->header('x-host');
        $hostName = $request->header('x-host-name');

        if ($host && $hostName) {
            $domain = DomainSupport::validateDomain($host);
            $subDomain = $domain->subDomain()->toString();

            if (Config::get('license-server.allow_subdomains') && !empty($subDomain)) {
                $request->merge([
                    'ls_domain' => $subDomain,
                ]);

                return $next($request);
            }

            $registrableDomain = $domain->registrableDomain()->toString();

            if (!empty($registrableDomain)) {
                $request->merge([
                    'ls_domain' => $registrableDomain,
                ]);

                return $next($request);
            }
        }

        return abort(403);
    }
}
