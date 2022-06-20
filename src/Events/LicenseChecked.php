<?php

namespace LaravelReady\LicenseServer\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

use LaravelReady\LicenseServer\Models\License;

class LicenseChecked
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(License $license, array|null $data = [])
    {
        $this->license = $license;
        $this->data = $data;
    }
}
