<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges\Provider\Coinbase;

use Kefzce\CryptoCurrencyExchanges\Converter\ObjectConverter;
use Kefzce\CryptoCurrencyExchanges\Provider\BaseProvider;
use Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\Resource\BuyPriceCurrencyResource;
use Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\Resource\CurrenciesResource;
use Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\Resource\CurrentAuthorizationResource;
use Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\Resource\CurrentServiceTimeResource;
use Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\Resource\CurrentUserResource;
use Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\Resource\ExchangeRatesResource;
use Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\Resource\SellPriceCurrencyResource;
use Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\Resource\SpotPriceCurrencyResource;
use Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\Resource\UserResource;
use Kefzce\CryptoCurrencyExchanges\Provider\ProviderInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

final class CoinbaseProvider extends BaseProvider implements ProviderInterface
{
    public const API_ENDPOINT = 'https://api.coinbase.com';
    public const PROVIDER_URI = 'https://www.coinbase.com';
    public const PROVIDER_DOC = 'https://developers.coinbase.com/api/v2';
    public const PROVIDER_FEES = 'https://support.coinbase.com/customer/portal/articles/2109597-buy-sell-bank-transfer-fees';

    /**
     * @var \Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\HttpClient
     */
    private $client;

    /**
     * @var \Kefzce\CryptoCurrencyExchanges\Converter\ObjectConverter
     */
    private $converter;

    /**
     * @param \Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\HttpClient $client
     * @param \Kefzce\CryptoCurrencyExchanges\Converter\ObjectConverter    $objectConverter
     */
    public function __construct(HttpClient $client, ObjectConverter $objectConverter)
    {
        $this->client = $client;
        $this->converter = $objectConverter;
    }

    /**
     * @param array  $params
     * @param string $classMap
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Safe\Exceptions\JsonException
     *
     * @return CurrenciesResource
     */
    public function getCurrencies(array $params = [], $classMap = CurrenciesResource::class): CurrenciesResource
    {
        return $this->getAndMapData('/v2/currencies', $params, $classMap);
    }

    /**
     * @param array  $params
     * @param string $classMap
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Safe\Exceptions\JsonException
     *
     * @return CurrentUserResource
     */
    public function getCurrentUser(array $params = [], $classMap = CurrentUserResource::class): CurrentUserResource
    {
        return $this->getAndMapData('/v2/user', $params, $classMap);
    }

    /**
     * @param null  $currency
     * @param array $params
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Safe\Exceptions\JsonException
     *
     * @return ExchangeRatesResource
     */
    public function getExchangeRates($currency = null, array $params = []): ExchangeRatesResource
    {
        if ($currency) {
            $params['currency'] = $currency;
        }

        return $this->getAndMapData('/v2/exchange-rates', $params, ExchangeRatesResource::class);
    }

    /**
     * @param null  $currency
     * @param array $params
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Safe\Exceptions\JsonException
     *
     * @return BuyPriceCurrencyResource
     */
    public function getBuyPrice($currency = null, array $params = []): BuyPriceCurrencyResource
    {
        if ($currency) {
            $pair = 'BTC-' . $currency;
        } else {
            $pair = 'BTC-USD';
        }

        return $this->getAndMapData('/v2/prices/' . $pair . '/buy', $params, BuyPriceCurrencyResource::class);
    }

    /**
     * @param null  $currency
     * @param array $params
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Safe\Exceptions\JsonException
     *
     * @return SellPriceCurrencyResource
     */
    public function getSellPrice($currency = null, array $params = []): SellPriceCurrencyResource
    {
        if ($currency) {
            $pair = 'BTC-' . $currency;
        } else {
            $pair = 'BTC-USD';
        }

        return $this->getAndMapData('/v2/prices/' . $pair . '/sell', $params, SellPriceCurrencyResource::class);
    }

    /**
     * @param string $currency|null
     * @param array  $params
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Safe\Exceptions\JsonException
     *
     * @return SpotPriceCurrencyResource
     */
    public function getSpotPrice(string $currency = null, array $params = []): SpotPriceCurrencyResource
    {
        if ($currency) {
            $pair = 'BTC-' . $currency;
        } else {
            $pair = 'BTC-USD';
        }

        return $this->getAndMapData('/v2/prices/' . $pair . '/spot', $params, SpotPriceCurrencyResource::class);
    }

    /**
     * @param array $params
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Safe\Exceptions\JsonException
     *
     * @return CurrentServiceTimeResource
     */
    public function getCurrentServiceTime(array $params = []): CurrentServiceTimeResource
    {
        return $this->getAndMapData('/v2/time', $params, CurrentServiceTimeResource::class);
    }

    /**
     * @param array $params
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Safe\Exceptions\JsonException
     *
     * @return CurrentAuthorizationResource
     */
    public function getCurrentAuthorization(array $params = []): CurrentAuthorizationResource
    {
        return $this->getAndMapData('/v2/user/auth', $params, CurrentAuthorizationResource::class);
    }

    /**
     * @param string $userId
     * @param array  $params
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Safe\Exceptions\JsonException
     *
     * @return UserResource
     */
    public function getUser(string $userId, array $params = []): UserResource
    {
        return $this->getAndMapData('/v2/users/' . $userId, $params, UserResource::class);
    }

    /**
     * @param string $path
     * @param array  $params
     * @param string $classMap
     *
     * @psalm-suppress MixedAssignment
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Kefzce\CryptoCurrencyExchanges\Converter\UnableToFindResourceException
     * @throws \Safe\Exceptions\JsonException
     *
     * @return array|mixed|object
     */
    private function getAndMapData(string $path, array $params, string $classMap)
    {
        $request = $this->client->request('GET', $path, $params);

        $bag = $this->decodeAndReturnBag($request);
        $object = $this->converter->convert(
            $bag->all(),
            $classMap
        );
        dump($bag->all());

        return $object;
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $request
     *
     * @throws \Safe\Exceptions\JsonException
     *
     * @psalm-suppress MixedAssignment
     * @psalm-suppress MixedArgument
     * @psalm-suppress MixedArrayAccess
     *
     * @return \Symfony\Component\HttpFoundation\ParameterBag
     */
    private function decodeAndReturnBag(ResponseInterface $request): ParameterBag
    {
        $response = $request->getBody()->getContents();
        dump($response);
        $rawData = \Safe\json_decode($response, true);

        return new ParameterBag($rawData['data']);
    }
}
