<?php

// src/Command/CurrencyRateCommand.php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Service\CurrencyRateManager;

class CurrencyRateCommand extends Command
{
    protected static $defaultName = 'app:currency:rates';

    private $currencyRateManager;

    public function __construct(CurrencyRateManager $currencyRateManager)
    {
        $this->currencyRateManager = $currencyRateManager;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('app:currency:rates')
            ->setDescription('Updates currency exchange rates.')
            ->addArgument('base_currency', InputArgument::REQUIRED, 'The base currency')
            ->addArgument('target_currencies', InputArgument::IS_ARRAY, 'The target currencies');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $baseCurrency = $input->getArgument('base_currency');
        $targetCurrencies = $input->getArgument('target_currencies');

        if (empty($targetCurrencies)) {
            $output->writeln('Error: At least one target currency must be specified.');
            return Command::FAILURE;
        }

        try {
            $this->currencyRateManager->updateExchangeRates($baseCurrency, $targetCurrencies);
            $output->writeln('Currency rates updated successfully for base currency ' . $baseCurrency);
        } catch (\Exception $e) {
            $output->writeln('Error updating currency rates: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

}
