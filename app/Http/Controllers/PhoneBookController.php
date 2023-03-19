<?php

namespace App\Http\Controllers;

use App\Models\PhoneBookName;
use App\Models\PhoneBookPhoneNumber;
use App\Models\PhoneBookVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PhoneBookController extends Controller
{
    public function show(Request $request): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application
    {
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
                $numbers = $numbers->sortBy(function ($number, $key) {
                    return $number->name->ruby;
                })->skip($DIRECTORY_MAX_ROWS*($now-1))->take($DIRECTORY_MAX_ROWS);

                return response()->view("phonebook.searchResult_xml", [
                    "numbers" => $numbers, "prevPage" => $prevPage, "nextPage" => $nextPage
                ])
                    ->header("Content-Type", "text/xml");
            }

        } else {
            // html request
            $names = $last_version->names->sortBy("ruby");
            return view("phonebook.show_html", ["names" => $names]);
        }
    }

    private function strip_number(string $number): string {
        return str_replace(["-", " ", "　"], "", $number);
    }

    public function addAll(Request $request): string|bool
    {
        DB::transaction(function () use ($request) {
            if ($request->boolean("overwrite")) {
                $last_version = PhoneBookVersion::create();
            } else {
                $last_version = PhoneBookVersion::query()
                    ->orderByDesc("id")
                    ->first();

            }
//        return $request->input("names");
            if (!is_array($request->input("names"))) return;
            foreach ($request->input("names") as $name) {
                if (!is_null($name["name"]) || !is_array($name["number"])) continue;
                $name_obj = $last_version->names()->create([
                    "name" => $name["name"],
                    "ruby" => $name["ruby"]?:""
                ]);
                foreach ($name["number"] as $number) {
                    if (!is_null($number["number"])) continue;
                    $name_obj->numbers()->create([
                        "type" => $number["type"]?:"",
                        "number" => $this->strip_number($number["number"])
                    ]);
                }
            }
        });
        return redirect()->route("phonebook.show");
    }

    public function getNameByNumber(Request $request, $number): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        $last_version = PhoneBookVersion::query()
            ->orderByDesc("id")
            ->first();
        $number = PhoneBookPhoneNumber::with("name")
            ->whereRelation("name", "version_id", "=", $last_version->id)
            ->where("number", "=", $number)->first();
        return response($number->name->name)->header("Content-Type", "text/plain");
    }


    public function edit(Request $request, $id = null): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|string|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        if ($request->method() == "POST") {
            // on POST
            $last_version = PhoneBookVersion::query()
                ->orderByDesc("id")
                ->first();
            //return $request->input("number");
            DB::transaction(function () use($last_version, $request) {
                if ($name = PhoneBookName::query()->find($request->integer("id"))) {
                    $name->update([
                        "name" => $request->string("name"),
                        "ruby" => mb_convert_kana($request->string("ruby"), "Hc")
                    ]);
                    $name->numbers()->delete();
                } else {
                    $name = $last_version->names()->create([
                        "name" => $request->string("name"),
                        "ruby" => mb_convert_kana($request->string("ruby"), "Hc")
                    ]);
                }


                foreach ($request->input("number") as $number) {
                    if (!is_null($number["number"])) {
                        $name->numbers()->create([
                            "type" => $number["type"],
                            "number" => $this->strip_number($number["number"])
                        ]);
                    }
                }
            });
            return redirect()->route("phonebook.show");
        } else if ($request->method() == "DELETE") {
            if ($name = PhoneBookName::query()->find($id)) {
                $name->delete();
            }
            return redirect()->route("phonebook.show");
        } else {
            // on GET
            if ($name = PhoneBookName::query()->find($id)) {
                return view("phonebook.edit_html", ["name"=>$name]);
            }
            return view("phonebook.edit_html", ["name"=>null]);
        }
    }
}
