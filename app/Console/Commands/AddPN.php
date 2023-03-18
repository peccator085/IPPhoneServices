<?php

namespace App\Console\Commands;

use App\Models\PhoneBookName;
use App\Models\PhoneBookVersion;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AddPN extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-pn';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        DB::transaction(function () {
            $version = PhoneBookVersion::create();
            $name = $version->names()->create(["name" => "山田　太郎", "ruby" => "やまだ　たろう"]);
            $number1 = $name->numbers()->create(["type"=>"自宅", "number"=>"050XXXXXXXX"]);
            $number2 = $name->numbers()->create(["type"=>"携帯", "number"=>"03XXXXXXXX"]);

            $name = $version->names()->create(["name" => "田中　雄一", "ruby" => "たなか　ゆういち"]);
            $number1 = $name->numbers()->create(["type"=>"自宅", "number"=>"050XXXXXXXX"]);
            $number2 = $name->numbers()->create(["type"=>"携帯", "number"=>"03XXXXXXXX"]);
        });

    }
}
