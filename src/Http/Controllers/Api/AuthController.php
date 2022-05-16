<?php

namespace LaravelReady\LicenseServer\Http\Controllers\Api;

use Illuminate\Http\Request;

use LaravelReady\LicenseServer\Models\IpAddress;
use LaravelReady\UltimateSupport\Support\IpSupport;
use LaravelReady\LicenseServer\Services\LicenseServer;
use LaravelReady\LicenseServer\Http\Controllers\ApiBaseController;

class AuthController extends ApiBaseController
{
    /**
     * Login with sanctum
     *
     * @param Request $request
     * @return Response
     */
    public function login(Request $request)
    {
        $request->validate([
            'license_key' => 'required|string|uuid',
        ]);

        $domain = $request->input('ls_domain');
        $licenseKey = $request->input('license_key');

        $license = LicenseServer::getLicense($domain, $licenseKey);

        if ($license) {
            $license->tokens()->where('name', $domain)->delete();

            $ipAddress = IpAddress::where('license_id', $license->id)->first();
            $serverIpAddress = IpSupport::getIP();

            if ($ipAddress && $ipAddress->ip_address == $serverIpAddress) {
                $licenseAccessToken = $license->createToken($domain, ['license-access']);

                return [
                    'status' => true,
                    'message' => 'Successfully logged in.',
                    'access_token' => explode('|', $licenseAccessToken->plainTextToken)[1],
                ];
            } else {
                IpAddress::create([
                    'license_id' => $license->id,
                    'ip_address' => $serverIpAddress,
                ]);
            }

            return response([
                'status' => false,
                'message' => 'This IP address is not allowed',
            ], 401);
        }

        return abort(401);
    }
}
