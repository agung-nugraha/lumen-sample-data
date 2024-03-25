<?php

namespace App\Console;

use App\Console\Commands\ArrayToCsv;
use App\Console\Commands\DownloadImages;
use App\Console\Commands\Order;
use App\Console\Commands\OrderApi;
use App\Console\Commands\OrderToCsv;
use App\Console\Commands\Pubsub\OrderPush;
use App\Console\Commands\Pubsub\ProductPush;
use App\Console\Commands\RabbitMq\OrderPull;
use App\Console\Commands\RabbitMq\OrderPush as AmqpOrderPush;
use App\Console\Commands\Swoole;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        OrderPush::class,
        OrderApi::class,
        Order::class,
        AmqpOrderPush::class,
        ArrayToCsv::class,
        OrderToCsv::class,
        OrderPull::class,
        Swoole::class,
        DownloadImages::class,
        ProductPush::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //
    }
}
