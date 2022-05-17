<?php

namespace LaravelReady\LicenseServer\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;

use LaravelReady\LicenseServer\Models\License;
use LaravelReady\LicenseServer\Support\DomainSupport;
use LaravelReady\LicenseServer\Exceptions\LicenseException;

class LicenseService
{
    public function __construct()
    {
    }

    /**
     * Validate the given domain.
     *
     * @param string $domain
     * @return null | string
     */
    public static function validateDomain(string $domain): null | string
    {
        if (empty($domain)) {
            return null;
        }

        $domain = DomainSupport::validateDomain($domain);
        $subDomain = $domain->subDomain()->toString();

        if (Config::get('license-server.allow_subdomains') && !empty($subDomain)) {
            return $subDomain;
        }

        $registrableDomain = $domain->registrableDomain()->toString();

        if (!empty($registrableDomain)) {
            return $registrableDomain;
        }

        return null;
    }

    /**
     * Add license to the given domain.
     *
     * @param string $domain
     * @param object $licensableModel
     * @param int $expirationDays
     * @param bool $idLifetime
     * @param bool $isTrial
     * @param int $userId
     *
     * @return null | License
     */
    public static function addLicense(
        string $domain,
        object $licensableModel,
        int $expirationDays = null,
        bool $isLifetime = false,
        bool $isTrial = false,
        int $userId = null
    ): null | License {
        $domain = self::validateDomain($domain);

        if ($domain === null) {
            return null;
        }

        if (self::getLicenseByDomain($domain) !== null) {
            throw new LicenseException('License already exists for this domain.');
        } else if ($licensableModel->licensable() === null) {
            throw new LicenseException('Given model is not licensable.');
        }

        $data = [
            'domain' => $domain,
            'license_key' => Str::uuid(),
            'is_lifetime' => false,
            'is_trial' => false,
            'user_id' => $userId,
        ];

        if (Config::get('license-server.allow_lifetime_licenses', true) && $isLifetime) {
            $data['is_lifetime'] = true;
            $data['expiration_date'] = null;
        } else if (Config::get('license-server.allow_trial_licenses', true) && $isTrial) {
            $expirationDays = $expirationDays ?? Config::get('license-server.trial_expiration_days', 365);

            $data['is_trial'] = true;
            $data['expiration_date'] = now()->addDays($expirationDays);
        } else {
            $expirationDays = $expirationDays ?? Config::get('license-server.license_expiration_days', 30);
            $data['expiration_date'] = now()->addDays($expirationDays);
        }

        $license = License::create($data);

        if ($license) {
            $licensableModel->licensable()->create([
                'license_id' => $license->id,
                'user_id' => $userId,
            ]);

            if (!$licensableModel->licensable) {
                $license->delete();
                throw new LicenseException('Failed to create license with product model.');
            }

            return $license;
        }

        return null;
    }

    /**
     * Get specific license.
     *
     * @param string $domain
     * @param string $licenseKey
     *
     * @return null | License
     */
    public static function getLicense(string $domain, string $licenseKey): null | License
    {
        $domain = self::validateDomain($domain);

        if ($domain === null || !Str::isUuid($licenseKey)) {
            return null;
        }

        return License::where([
            ['domain', '=', $domain],
            ['license_key', '=', $licenseKey],
        ])->first();
    }

    /**
     * Get license by key.
     *
     * @param string $licenseKey
     *
     * @return null | License
     */
    public static function getLicenseByKey(string $licenseKey): null | License
    {
        if (!Str::isUuid($licenseKey)) {
            return null;
        }

        return License::where('license_key', $licenseKey)->first();
    }

    /**
     * Get license by domain.
     *
     * @param string $domain
     *
     * @return null | License
     */
    public static function getLicenseByDomain(string $domain): null | License
    {
        $domain = self::validateDomain($domain);

        if ($domain === null) {
            return null;
        }

        return License::where('domain', $domain)->orderBy('id')->first();
    }

    /**
     * Get licenses by multiple domains.
     *
     * @param array $domains
     */
    public static function getLicensesByDomains(array $domains): Collection
    {
        foreach ($domains as $key => $domain) {
            $domains[$key] = self::validateDomain($domain);

            $domains[$key] = self::getLicenseByDomain($domain);
        }

        return collect($domains);
    }

    /**
     * Check if license is valid.
     * Returns "active", "inactive", "suspended", "expired" or null.
     *
     * @param string $domain
     * @param string $licenseKey
     *
     * @return string
     */
    public static function checkLicenseStatus(string $domain, string $licenseKey)
    {
        $license = self::getLicense($domain, $licenseKey);

        if ($license === null || !Str::isUuid($licenseKey)) {
            return null;
        }

        if ($license->expiration_date < now()) {
            return 'expired';
        }

        return $license->status;
    }

    /**
     * Check if license is valid.
     *
     * @param string $licenseKey
     * @param string $status
     *
     * @return null | License
     */
    public static function setLicenseStatus(string $licenseKey, string $status): null | License
    {
        if (!Str::isUuid($licenseKey)) {
            return null;
        }

        $license = self::getLicenseByKey($licenseKey);

        if ($license->expiration_date > now()) {
            $license->status = $status;
            $license->save();

            return $license;
        }

        return null;
    }
}
