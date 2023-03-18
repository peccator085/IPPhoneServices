<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class kanatest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:kanatest';

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
        print mb_convert_kana("コングﾁｪｰしょん", "Hc");
    }
}
