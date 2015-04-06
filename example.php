<?php
include('itbit.php');

$itbit = new itbit('your-itbit-api-secret','your-itbit-api-client-id','your-ibit-user-id');

// Get all wallets
echo '<h2>Get all wallets</h2><pre>';
print_r($itbit->wallet());
echo '</pre>';

sleep(1); // Wait so that we can get new nonce

echo '<h2>Get a single wallet</h2><pre>';
print_r($itbit->wallet('ab09412e-4129-4d6a-bca4-eff5e22cad96a'));
echo '</pre>';

sleep(1); // Wait so that we can get new nonce

echo '<h2>Get a balance</h2><pre>';
print_r($itbit->balance('ab09412e-4129-4d6a-bca4-eff5e22cad96a','USD'));
echo '</pre>';

sleep(1); // Wait so that we can get new nonce

echo '<h2>Get gets orders for a wallet</h2><pre>';
print_r($itbit->orders('ab09412e-4129-4d6a-bca4-eff5e22cad96a'));
echo '</pre>';

sleep(1); // Wait so that we can get new nonce

echo '<h2>Get gets trades for a wallet</h2><pre>';
print_r($itbit->trades('ab09412e-4129-4d6a-bca4-eff5e22cad96a'));
echo '</pre>';

sleep(1); // Wait so that we can get new nonce

echo '<h2>Cancel an open order</h2><pre>';
print_r($itbit->cancel('ab09412e-4129-4d6a-bca4-eff5e22cad96a','abe1124214-1424-1f42-dfe1-142415512'));
echo '</pre>';

sleep(1); // Wait so that we can get new nonce

echo '<h2>Create an order</h2><pre>';
// Create an order to sell 1.0042 BTC at a price of $259.92
print_r($itbit->create_order('ab09412e-4129-4d6a-bca4-eff5e22cad96a','sell', 1.0042, 259.92));
echo '</pre>';

echo '<h2>Withdrawl some BTC</h2><pre>';
print_r($itbit->withdraw('ab09412e-4129-4d6a-bca4-eff5e22cad96a',1.492,'1FqfWC3oX2jybUvtsggNtct7MciKJpM6yh'));
echo '</pre>';

sleep(1); // Wait so that we can get new nonce

echo '<h2>Get a deposit address</h2><pre>';
print_r($itbit->deposit('ab09412e-4129-4d6a-bca4-eff5e22cad96a'));
echo '</pre>';

