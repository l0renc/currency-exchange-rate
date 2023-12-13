<?php
// src/Controller/CurrencyController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Service\CurrencyRateManager;
use Symfony\Component\Routing\Annotation\Route;

class CurrencyRateController extends AbstractController
{
    private $currencyManager;

    public function __construct(CurrencyRateManager $currencyManager)
    {
        $this->currencyManager = $currencyManager;
    }

    /**
     * @Route("/api/exchange-rates", name="get_exchange_rates", methods={"GET"})
     */
    public function getExchangeRates(Request $request): Response
    {
        try {
            $baseCurrency = $request->query->get('base_currency', 'default_base_currency');
            $targetCurrencies = explode(',', $request->query->get('target_currencies', ''));

            $rates = $this->currencyManager->getExchangeRates($baseCurrency, $targetCurrencies);

            return $this->json($rates);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'An error occurred: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
