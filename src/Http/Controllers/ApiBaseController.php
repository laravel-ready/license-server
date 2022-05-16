<?php

namespace LaravelReady\LicenseServer\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class ApiBaseController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
