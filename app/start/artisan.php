<?php

/*
|--------------------------------------------------------------------------
| Register The Artisan Commands
|--------------------------------------------------------------------------
|
| Each available Artisan command must be registered with the console so
| that it is available to be called. We'll register every command so
| the console gets access to each of the command object instances.
|
*/
// Tickers
Artisan::add(new ExchangeBitstampTickerCommand());
Artisan::add(new ExchangeBtceTickerCommand());
Artisan::add(new ExchangeMtgoxTickerCommand());
Artisan::add(new ExchangeLocalbitcoinsTickerCommand());
Artisan::add(new ExchangeKrakenTickerCommand());
Artisan::add(new ExchangeCampbxTickerCommand());
Artisan::add(new ExchangeBitcurexTickerCommand());
Artisan::add(new ExchangeBtcchinaTickerCommand());
Artisan::add(new ExchangeAllTickerCommand());

// Historical
Artisan::add(new ExchangeBitstampHistoricalCommand());
Artisan::add(new ExchangeBtceHistoricalCommand());
Artisan::add(new ExchangeMtgoxHistoricalCommand());
Artisan::add(new ExchangeCampbxHistoricalCommand());
Artisan::add(new ExchangeKrakenHistoricalCommand());
Artisan::add(new ExchangeBitcurexHistoricalCommand());
Artisan::add(new ExchangeBtcchinaHistoricalCommand());

// Gather trades
Artisan::add(new ExchangeBtceTransactionsCommand());
Artisan::add(new ExchangeBitcurexTransactionsCommand());
Artisan::add(new ExchangeBitstampTransactionsCommand());
Artisan::add(new ExchangeBtcchinaTransactionsCommand());
Artisan::add(new ExchangeKrakenTransactionsCommand());
Artisan::add(new ExchangeLocalbitcoinsTransactionsCommand());

// Aggregate transactions
Artisan::add(new AggregateTransactionsCommand());

// Compress exchanges data
Artisan::add(new CompressExchangesDataCommand());

// Summary command
Artisan::add(new UpdateExchangesSummaryCommand());

Artisan::add(new RecalculateAllExchangesCommand());