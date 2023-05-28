<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PhoneState extends Controller
{
    public function get(Request $request) {
        $ROUTER_USER = env("ROUTER_USER");
        $ROUTER_PWD = env("ROUTER_PWD");
        $ROUTER_IP = env("ROUTER_IP");
        $ch = curl_init("http://{$ROUTER_USER}:{$ROUTER_PWD}@{$ROUTER_IP}/index.cgi/system_tel.log");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $c = curl_exec($ch);
        $c = mb_convert_encoding($c, "utf8", "shift-jis");
        $ca = explode("\n", $c);
        $status = "";
        $number = "";
        foreach (array_reverse($ca) as $line) {
            if (str_contains($line, "切断") || str_contains($line, "放棄")) {
                $status = "Disconnected";
                break;
            }
            if (str_contains($line, "着信")) {
                $status = "Incoming";
                $number = array_reverse(explode(" ", $line))[0];
                break;
            }
        }
        if ($status == "") $status = "Disconnected";

        return response("{$status} {$number}", 200);
    }
    //
}
