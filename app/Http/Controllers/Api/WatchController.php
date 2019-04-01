<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Api\Request;
use Illuminate\Support\Facades\Storage;

class WatchController extends Controller
{
    public function deviceGetServerIp(Request $request)
    {
        return $this->response->array([
            'status' => 0,
            'ip' => '47.88.86.209',
        ]);
    }

    public function deviceUploadTime(Request $request)
    {
        return $this->response->array([
            'status' => 0,
            'value' => microtime(),
        ]);
    }

    public function deviceVersionUpdate(Request $request)
    {
        return $this->response->array([
            'status' => 0,
        ]);
    }

    public function deviceGetQRcode(Request $request)
    {
        return Storage::disk('public')->download('qrcode/Ubuntu.jpg');
    }

    public function deviceParamInit(Request $request)
    {
        return $this->response->array([
            "status" => "0",
            "connect" => [
                "beat" => 240, "timeout" => 60, "retry" => 3, "restart" => 1,
            ],
            "common" => [
                "bright" => 2, "sound" => 3, "shake" => 1, "quiet" => 1,
                "powerAuto" => ["8 => 00|14 => 00", "12 => 00|21 => 00"],
                "locationOnff" => 1, "heartOnff" => 1, "walkOnff" => 1,
                "sittingOnff" => 1, "sleepOnff" => 1, "timezone" => "GMT+8",
            ],
            "frequency" => [
                "signalGfreq" => 180, "signalUfreq" => 300, "batteryGfreq" => 60,
                "batteryUfreq" => 60, "heartGfreq" => 600, "heartUfreq" => 600,
                "walkUfreq" => 30, "locationGfreq" => 30, "locationUfreq" => 300,
            ],
            "health" => [
                "height" => 172, "weight" => 65, "sittingTime" => 2,
                "sleepSpan" => ["20:00-08:00", "13:00-15:00"],
            ],
        ]);
    }
}
