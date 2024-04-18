<?php

namespace App\Libraries\Xtract;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Cache;

class Proto
{
    private static ?Client $client = null;
    public static string $token;
    public static string $bearerToken = 'AAAAAAAAAAAAAAAAAAAAANRILgAAAAAAnNwIzUejRCOuH5E6I8xnZz4puTs%3D1Zv7ttfk8LF81IUq16cHjhLTvJu4FA33AGWWjCpTnA';

    /**
     * @throws GuzzleException
     */
    public static function details(string $username): array
    {
        return self::request(Payloads::UserByScreenName($username));
    }

    /**
     * @throws GuzzleException
     */
    public static function tweet(string $id): Tweet
    {
        return new Tweet(self::request(Payloads::TweetResultByRestId($id))['data']['tweetResult']['result']);
    }

    /**
     * @throws GuzzleException
     */
    private static function getToken(): string
    {
        self::$token = Cache::get('guest_token', 600);

        if (!self::$token || true) {
            $request = new Request('POST', 'https://api.twitter.com/1.1/guest/activate.json', [
                'authorization' => 'Bearer ' . self::$bearerToken
            ]);

            $res = self::getHTTPClient()->send($request);
            self::$token = json_decode((string)$res->getBody(), true)['guest_token'];

            Cache::set('guest_token', self::$token);
        }

        return self::$token;
    }

    private static function getHTTPClient(): Client
    {
        if (!self::$client) {
            self::$client = new Client([
                'base_uri' => 'https://api.twitter.com',
                'timeout' => 5.0,
            ]);
        }

        return self::$client;
    }

    /**
     * @throws GuzzleException
     */
    private static function request(array $payload)
    {
        $request = new Request('GET', "${payload['endpoint']}${payload['params']}", [
            'authorization' => 'Bearer ' . self::$bearerToken,
            'x-guest-token' => self::getToken(),
            'x-twitter-active-user' => 'yes',
            'x-twitter-client-language' => 'en'
        ]);

        try {
            $res = self::getHTTPClient()->send($request);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            dd((string)$e->getResponse()->getBody()->getContents());
        } catch (GuzzleException $e) {
            dd($e);
        }

        return json_decode($res->getBody(), true);
    }
}
