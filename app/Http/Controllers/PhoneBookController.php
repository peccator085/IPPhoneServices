<?php

namespace App\Http\Controllers;

use App\Models\PhoneBookName;
use App\Models\PhoneBookPhoneNumber;
use App\Models\PhoneBookVersion;
use Illuminate\Http\Request;

class PhoneBookController extends Controller
{
    public function show(Request $request) {
        $last_version = PhoneBookVersion::query()
            ->orderByDesc("id")
            ->first();

        $head_regex = [
            "あ" => "/^[あ-おゔ]/u",
            "か" => "/^[か-ご]/u",
            "さ" => "/^[さ-ぞ]/u",
            "た" => "/^[た-ど]/u",
            "な" => "/^[な-の]/u",
            "は" => "/^[は-ぽ]/u",
            "ま" => "/^[ま-も]/u",
            "や" => "/^[や-よ]/u",
            "ら" => "/^[ら-ろ]/u",
            "わ" => "/^[わ-ん]/u",
            "英数" => "/^[A-Za-z0-9]/",
        ];

        if (str_starts_with($request->header("Accept"), "x-CiscoIPPhone")) {
            // xml request
            $DIRECTORY_MAX_ROWS = 32;

            if (!$request->query->has("gyo")) {
                // 電話帳ホーム
                return response()->view("phonebook.show_xml")->header("Content-Type", "text/xml");
            } else {
                // gyoにより処理分岐する
                $numbers = PhoneBookPhoneNumber::with("name")
                    ->whereRelation("name", "version_id", "=", $last_version->id)
                    ->get();
                if ($request->query("gyo") == "英数") {
                    $numbers = $numbers->filter(function ($number, $key) use ($request, $head_regex) {
                        return preg_match($head_regex[$request->query("gyo")], $number->name->name);
                    });
                } else if ($request->query("gyo") == "その他") {
                    $numbers = $numbers->filter(function ($number, $key) {
                        return $number->name->ruby == "";
                    });
                } else if (array_key_exists($request->query("gyo"), $head_regex)) {
                    $numbers = $numbers->filter(function ($number, $key) use ($request, $head_regex) {
                        return preg_match($head_regex[$request->query("gyo")], $number->name->ruby);
                    });
                }
                $pages = ceil($numbers->count() / $DIRECTORY_MAX_ROWS);
                $now = intval($request->query("page", "1"));
                $prev = $now<=1||$pages<=$now?1:$now-1;
                $prevPage = route("phonebook.show", ["gyo"=>$request->query("gyo"), "page"=>$prev]);
                $next = $now>=$pages?$pages:$now+1;
                $nextPage = route("phonebook.show", ["gyo"=>$request->query("gyo"), "page"=>$next]);
                $numbers = $numbers->skip($DIRECTORY_MAX_ROWS*($now-1))->take($DIRECTORY_MAX_ROWS);

                return response()->view("phonebook.searchResult_xml", [
                    "numbers" => $numbers, "prevPage" => $prevPage, "nextPage" => $nextPage
                ])
                    ->header("Content-Type", "text/xml");
            }

        } else {
            // html request
            $names = $last_version->names;
            return view("phonebook.show_html", ["names" => $names]);

        }

    }

    public function getNameByNumber(Request $request, $number) {
        $last_version = PhoneBookVersion::query()
            ->orderByDesc("id")
            ->first();
        $number = PhoneBookPhoneNumber::with("name")
            ->whereRelation("name", "version_id", "=", $last_version->id)
            ->where("number", "=", $number)->first();
        return response($number->name->name)->header("Content-Type", "text/plain");
    }
    //
}
