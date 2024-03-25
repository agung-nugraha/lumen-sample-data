<?php

namespace App\Service\Pubsub;

use Google\Cloud\PubSub\PubSubClient;
use Google\Cloud\PubSub\Subscription;
use Google\Cloud\PubSub\Topic;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

abstract class Pubsub
{
    protected PubSubClient $client;
    protected Topic $topic;
    protected Subscription $subscription;

    public function __construct()
    {
        $keyFile = storage_path(env('PUBSUB_KEY'));
        if (!is_file($keyFile)) {
            throw new FileException("Service account key doesn't exist.");
        }

        $options = [
            'projectId' => env('PUBSUB_ID'),
            'keyFilePath' => $keyFile
        ];
        $this->client = new PubSubClient($options);
    }

    protected function getTopic(string $name)
    {
        return $this->client->topic($name);
    }

    protected function getSubcription(string $name)
    {
        return $this->client->subscription($name);
    }

    abstract public function push();

    abstract public function fetch();
}
