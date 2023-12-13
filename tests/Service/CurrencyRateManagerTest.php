<?php

// tests/Service/CurrencyManagerTest.php

namespace App\Tests\Service;

use App\Repository\CurrencyRateRepository;
use App\Service\CurrencyRateManager;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Predis\Client;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class CurrencyRateManagerTest extends TestCase
{
    private CurrencyRateRepository $currencyRateRepositoryMock;
    private $httpClientMock;
    private $entityManagerMock;
    private $redisClientMock;
    private $currencyRateManager;

    protected function setUp(): void
    {
        $this->currencyRateRepositoryMock = $this->createMock(CurrencyRateRepository::class);
        $this->httpClientMock = $this->createMock(HttpClientInterface::class);
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $this->redisClientMock = $this->createMock(Client::class);

        $this->currencyRateManager = new CurrencyRateManager(
            $this->currencyRateRepositoryMock,
            $this->httpClientMock,
            $this->redisClientMock,
            $this->entityManagerMock
        );
    }

    public function testGetExchangeRates()
    {
        $currencyRateManager = $this->createMock(CurrencyRateManager::class);
        $currencyRateManager->method('getExchangeRates')
            ->willReturn(['USD' => 1.12]);

        $this->assertEquals(['USD' => 1.12], $currencyRateManager->getExchangeRates('EUR', ['USD']));
    }

    public function testUpdateExchangeRates()
    {
        $baseCurrency = 'EUR';
        $targetCurrencies = ['USD', 'GBP', 'JPY'];
        $mockRates = ['USD' => 1.12, 'GBP' => 0.85, 'JPY' => 130.0];

        // Mocking the HTTP client response
        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getContent')
            ->willReturn(json_encode(['rates' => $mockRates]));

        $this->httpClientMock->expects($this->once())
            ->method('request')
            ->with('GET', 'https://api.frankfurter.app/latest', [
                'query' => [
                    'from' => $baseCurrency,
                    'to' => implode(',', $targetCurrencies),
                ]
            ])
            ->willReturn($responseMock);

        // Execute the updateExchangeRates method
        $this->currencyRateManager->updateExchangeRates($baseCurrency, $targetCurrencies);

    }
}
