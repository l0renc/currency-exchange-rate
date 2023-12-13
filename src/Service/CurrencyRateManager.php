<?php
// src/Service/CurrencyManager.php

namespace App\Service;

use App\Entity\CurrencyRate;
use App\Repository\CurrencyRateRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Predis\Client;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CurrencyRateManager
{
    private CurrencyRateRepository $currencyRateRepository;
    private HttpClientInterface $httpClient;
    private Client $redis;

    private EntityManagerInterface $entityManager;

    public function __construct(CurrencyRateRepository $currencyRateRepository, HttpClientInterface $httpClient, Client $redis, EntityManagerInterface $entityManager)
    {
        $this->currencyRateRepository = $currencyRateRepository;
        $this->httpClient = $httpClient;
        $this->redis = $redis;
        $this->entityManager = $entityManager;
    }

    public function getExchangeRates(string $baseCurrency, array $targetCurrencies): array
    {
        $cachedRates = $this->fetchFromCache($baseCurrency, $targetCurrencies);
        $rates = [];

        foreach ($targetCurrencies as $currency) {
            if (isset($cachedRates[$currency]) && $cachedRates[$currency] !== null) {
                // If the rate is in the cache and not null
                $rates[$currency] = $cachedRates[$currency];
            } else {
                // If the rate is not in the cache or is null, fetch from DB
                $rate = $this->currencyRateRepository->findRate($baseCurrency, $currency);

                if ($rate === null) {
                    // If the rate is not in the DB, fetch from API
                    $rate = $this->fetchFromApi($baseCurrency, [$currency]);
                    $this->saveRate($currency,$rate[$currency]);
                }

                $this->cacheRate($currency,$rate[$currency] ?? $rate);
                $rates[$currency] = $rate[$currency] ?? $rate;
            }
        }

        return $rates;
    }

    public function updateExchangeRates(string $baseCurrency, array $targetCurrencies): void
    {
        $rates = $this->fetchFromApi($baseCurrency, $targetCurrencies);
        $this->saveRates($rates);
        $this->cacheRates($rates);
    }

    private function fetchFromCache(string $baseCurrency, array $targetCurrencies): array
    {
        // Sample logic for Redis (pseudo-code)
        $rates = [];
        foreach ($targetCurrencies as $currency) {
            $rate = $this->redis->get("{$baseCurrency}_{$currency}");
            if ($rate !== false) {
                $rates[$currency] = $rate;
            }
        }
        return $rates;
    }

    private function fetchFromApi(string $baseCurrency, array $targetCurrencies): array
    {

            $url = 'https://api.frankfurter.app/latest';
            $query = [
                'from' => $baseCurrency,
                'to' => implode(',', $targetCurrencies),
            ];

            $response = $this->httpClient->request('GET', $url, ['query' => $query]);
            $data = json_decode($response->getContent(), true);

            return $data['rates'] ?? [];

    }

    private function saveRates(array $rates): void
    {

        foreach ($rates as $currency => $rate) {
            // Create or update the CurrencyRate entity
            $currencyRate = $this->currencyRateRepository->findOneBy(['baseCurrency' => 'EUR', 'targetCurrency' => $currency]) ?? new CurrencyRate();

            $currencyRate->prePersist();
            $currencyRate->setBaseCurrency('EUR');
            $currencyRate->setTargetCurrency($currency);
            $currencyRate->setRate($rate);


            $this->entityManager->persist($currencyRate);
        }
        $this->entityManager->flush();
    }

    private function saveRate($currency, $rate): void
    {
            // Create or update the CurrencyRate entity
        $currencyRate = $this->currencyRateRepository->findOneBy(['baseCurrency' => 'EUR', 'targetCurrency' => $currency]) ?? new CurrencyRate();

        $currencyRate->prePersist();
        $currencyRate->setBaseCurrency('EUR');
        $currencyRate->setTargetCurrency($currency);

        $currencyRate->setRate($rate);

        $this->entityManager->persist($currencyRate);

        $this->entityManager->flush();
    }

    private function cacheRates(array $rates): void
    {
        foreach ($rates as $currency => $rate) {

            // Set the key with a 24-hour expiration (86400 seconds)
            $this->redis->set("EUR_{$currency}", $rate, 'EX', 86400);
        }

    }

    private function cacheRate($currency, $rate): void
    {
        // Set the key with a 24-hour expiration (86400 seconds)
        $this->redis->set("EUR_{$currency}", $rate, 'EX', 86400);

    }

}
