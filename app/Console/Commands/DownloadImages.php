<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Console\Command;

class DownloadImages extends Command
{
    protected $signature = 'image:download';

    protected $description = 'Download image from url';

    public function handle()
    {
        $requests = function ($remoteImagesUrl) {
            foreach (array_filter($remoteImagesUrl) as $url) {
                yield new Request('GET', $url['image_url']);
            }
        };

        $imagesUrl = json_decode(
            file_get_contents(storage_path('image_url.json')),
            true
        );
        $images = [];
        $pool = new Pool(new Client([
            'headers' => ['Connection' => 'close'],
            CURLOPT_FORBID_REUSE => true,
            CURLOPT_FRESH_CONNECT => true,
        ]), $requests($imagesUrl), [
            'fulfilled' => function (Response $response, $index) use (&$images, $imagesUrl) {
                if ($response->getStatusCode() >= 200 && $response->getStatusCode() <= 400) {
                    $images[] = $imagesUrl[$index] ?? null;
                }
                $response->getBody()->close();
            },
            'rejected' => function ($reason, $index) {
                return;
            }
        ]);

        // Initiate the transfers and create a promise
        $promise = $pool->promise();
        // Force the pool of requests to complete.
        $promise->wait();

        echo json_encode($images);
    }
}
