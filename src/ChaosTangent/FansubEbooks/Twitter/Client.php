<?php

namespace ChaosTangent\FansubEbooks\Twitter;

use GuzzleHttp\Client as GuzzleClient,
    GuzzleHttp\HandlerStack,
    GuzzleHttp\Subscriber\Oauth\Oauth1;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Minimal Twitter client
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class Client
{
    protected $options;
    protected $client;

    public function __construct(array $options)
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->options = $resolver->resolve();

        $handlerStack = HandlerStack::create();
        $handlerStack->push(new Oauth1([
            'consumer_key' => $this->options['consumer_key'],
            'consumer_secret' => $this->options['consumer_secret'],
            'token' => $this->options['access_token'],
            'token_sercet' => $this->options['secret_token'],
        ]));

        $this->client = new Client([
            'base_uri' => 'https://api.twitter.com/1.1/',
            'auth' => 'oauth',
            'handler' => $handlerStack,
        ]);
    }

    /**
     * Tweet a status
     *
     * @param string $status
     * @throws RequestException
     * @return object The response from the Twitter API
     * @see https://dev.twitter.com/rest/reference/post/statuses/update
     */
    public function tweet($status)
    {
        $resp = $this->client->post('statuses/update.json', [
            'form_params' => [
                'status' => urlencode($status),
                'trim_user' => 'true',
            ],
        ]);

        $json = json_decode($resp->getBody(), false);
        return $json;
    }

    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired([
            'app_id', 'consumer_key', 'consumer_secret', 'access_token', 'secret_token',
        ]);
    }
}
