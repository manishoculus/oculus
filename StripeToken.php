<?php
$CUSTOMER_ID = "cus_9ZFeAWbyO0aAva";
$CARD_ID    = "card_19G78qILIUequ5ACOALRhw5t";
$CONNECTED_STRIPE_ACCOUNT_ID = "acct_18BhHmAKK0VwY9Oo";
$secret_key = "sk_test_gGgKXB7UrtKWXYUOyWVn00fz";
require_once('stripe-payment-gateway-php/Stripe/lib/Stripe.php');
try{
	Stripe::setApiKey($secret_key); //Replace with your Secret Key
	$arrToken['token']  = Stripe_Token::create(
		array("customer" => $CUSTOMER_ID, "card" => $CARD_ID),
		array("stripe_account" => $CONNECTED_STRIPE_ACCOUNT_ID) // id of the connected account
	);
}catch(Exception $e){
	$arrToken['error'] = $e;
}
print_r($arrToken);
