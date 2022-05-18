<?php

namespace LaravelReady\LicenseServer\Support;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

use Pdp\Domain;
use Pdp\TopLevelDomains;
use Pdp\ResolvedDomain;

class DomainSupport
{
    private static $publicSuffixList = 'license-server/iana-tld-list.txt';

    /**
     * Validate the given domain as tld, subdomain and registrable domain.
     *
     * @param string $domain
     *
     * @return ResolvedDomain
     */
    public static function validateDomain(string $domain): ResolvedDomain
    {
        if (!Storage::exists(self::$publicSuffixList)) {
            self::checkTldCache();
        }

        $parsedUrl = parse_url(Str::lower($domain));

        $domain = $parsedUrl['host'] ?? $parsedUrl['path'];

        $topLevelDomains = TopLevelDomains::fromPath(Storage::path(self::$publicSuffixList));
        $domain = Domain::fromIDNA2008($domain);

        return $topLevelDomains->resolve($domain);
    }

    /**
     * Check if the tld domain list cache is up to date.
     */
    public static function checkTldCache(): bool
    {
        if (Storage::exists(self::$publicSuffixList)) {
            return true;
        }

        $response = Http::get('https://data.iana.org/TLD/tlds-alpha-by-domain.txt');

        if ($response->status() === 200) {
            return Storage::put(self::$publicSuffixList, $response->body());
        }

        return false;
    }
}
