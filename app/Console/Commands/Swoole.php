<?php

namespace App\Console\Commands;

use Swoole\Coroutine;
use Illuminate\Console\Command;

use function Swoole\Coroutine\{run, go};

class Swoole extends Command
{
    protected $signature = 'swoole:run';

    protected $description = 'Run swoole.';

    public function handle()
    {
        run(function () {
            for ($i = 0; $i < 2000; $i++) {
                go(function () {
                    sleep(3);
                });
            }
            echo count(Coroutine::listCoroutines()), ' active coroutines when reaching the end of the PHP script.', PHP_EOL;
        });
    }
}
