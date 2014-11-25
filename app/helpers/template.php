<?php

function currency($amount, $currency, $decimals = 2)
{
	return sprintf('%s%s', $currency, number_format($amount, $decimals));
}