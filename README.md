ebanc-php
=========

PHP bindings for the eBanc API

Installation
------------
When using PHP bindings for the eBanc API, there are a two main ways to use in your project. The first way is to just download the Ebanc.php file and require it in your project. The other way is via composer.

#### Composer
You can add this to your project via composer by including the following information in your composer.json file:

    "require": {
        "ebanc/ebanc-php": "dev-master"
    }

Usage
-----

#### Initalize
You initalize the API client in the following way:

    require_once('Ebanc.php');
    
    $apiKey    = '123456789';
    $gatewayId = 'a01';
    $ebanc = new Ebanc($apiKey, $gatewayId);


#### Customers

Get a list of all this account's customers

    $customers = $ebanc->getCustomers();

Get a specific customer's details:

    $uuid = '03ae8670-27d3-0132-54de-1040f38cff7c';
    $customer = $ebanc->getCustomer($uuid);
    
    //if we found a customer
    if($customer){
      echo 'Found '.$customer['first_name'].' '.$customer['last_name']
    }else{
      //this usually means that the customer was not found
      echo $ebanc->getError();
    }

Create a customer and get the uuid:

    $firstName     = 'Steve';
    $lastName      = 'Bobs';
    $routingNumber = '123456789';
    $accountNumber = '123456';
    
    $customer = $ebanc->createCustomer($firstName, $lastName, $routingNumber, $accountNumber);

    if($customer){
      echo 'Created customer '.$customer['first_name'].' '.$customer['last_name'].' with the UUID of '.$customer['uuid'];
    }else{
      echo $ebanc->getError();
    }

Update a customer:

    $uuid          = '03ae8670-27d3-0132-54de-1040f38cff7c';
    $firstName     = 'Steve';
    $lastName      = 'Bobs';
    $routingNumber = '123456789';
    $accountNumber = '123456';
    
    $customer = $ebanc.updateCustomer($uuid, $firstName, $lastName, $routingNumber, $accountNumber);
    
    //The Routing Number and Account Number are optional params you can change just a customer's name
    //Example: $customer = $ebanc.updateCustomer($uuid, $firstName, $lastName);
    
    if($customer){
      echo 'Updated customer '.$customer['first_name'].' '.$customer['last_name'].' with the UUID of '.$customer['uuid'];
    }else{
      echo $ebanc->getError();
    }


#### Transactions

Get a list of all this account's last 50 transactions

    $transactions = $ebanc->getTransactions();
    echo 'Found '.count($transactions).' Transactions';

Get a the latest information about a specific transaction

    $uuid = '03ae8670-27d3-0132-54de-1040f38cff7c';
    $transaction = $ebanc->getTransaction($transaction_uuid);
    
    if($transaction){
      echo 'Transaction for '.$transaction['amount'].' with the UUID of '.$transaction['uuid'].' was found';
    }else{
      echo $ebanc->getError();
    }

##### Creating Transactions
When creating a transaction you can either pass in all customer details or simply pass in the uuid for an already created customer. Sometimes it makes sense to just pass in all of the details. This is usually in the case of a single transaction. Other times it makes more sense to store the customer details and just store that uuid on your server to pass in at payment time. This is a good approch when you will have returning customers or need to setup some kind of a schedule, but don't want to store that sensitive information on your server.

Create Transaction by passing in all details.

    $firstName     = 'Steve';
    $lastName      = 'Bobs';
    $routingNumber = '123456789';
    $accountNumber = '123456';
    $amount        = '150.92';
    
    $transaction = $ebanc->createTransaction($firstName, $lastName, $routingNumber, $accountNumber, $amount);
    
    if($transaction){
      echo 'Transaction for '.$transaction['amount'].' with the UUID of '.$transaction['uuid'].' was created';
    }else{
      echo $ebanc->getError();
    }

###### Types, Categories and, Memos
Transaction type can be a debit or credit. If you do not pass in a transaction type, debit is defaulted.

A category and memo can be used together or seperate to help you with reporting later. The category helps group transaction types together (Example: "Online orders" and "In-store orders"). The memo helps discribe that specific transaction (Example: Put in the ID number of order from your eCommerce or POS system to tie that transaction to the correct order).

Create Transaction by passing in all details and optional category and/or memo:

    $firstName     = 'Steve';
    $lastName      = 'Bobs';
    $routingNumber = '123456789';
    $accountNumber = '123456';
    $amount        = '150.92';
    $type          = 'debit';
    $category      = 'Online Orders';
    $memo          = 'Order# 1234';
    
    $transaction = $ebanc->createTransaction($firstName, $lastName, $routingNumber, $accountNumber, $amount, $type, $category, $memo);
    
    if($transaction){
      echo 'Transaction for '.$transaction['amount'].' with the UUID of '.$transaction['uuid'].' was created';
    }else{
      echo $ebanc->getError();
    }

###### Customer UUID
Create Transaction by passing in customer UUID:

    $uuid   = '03ae8670-27d3-0132-54de-1040f38cff7c';
    $amount = '51.50';
    
    $transaction = $ebanc->createTransactionForCustomer($uuid, $amount);
    
    if($transaction){
      echo 'Transaction for '.$transaction['amount'].' with the UUID of '.$transaction['uuid'].' was created';
    }else{
      echo $ebanc->getError();
    }

Create Transaction by passing in customer UUID and optional type, category and/or memo:

    $uuid     = '03ae8670-27d3-0132-54de-1040f38cff7c';
    $amount   = '51.50';
    $type     = 'debit';
    $category = 'Online Orders';
    $memo     = 'Order# 1234';
    
    $transaction = $ebanc->createTransactionForCustomer($uuid, $amount, $type, $category, $memo);
    
    if($transaction){
      echo 'Transaction for '.$transaction['amount'].' with the UUID of '.$transaction['uuid'].' was created';
    }else{
      echo $ebanc->getError();
    }
