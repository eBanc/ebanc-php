ebanc-php
=========

PHP bindings for the eBanc API

Installation
------------
When using PHP bindings for the eBanc API, there are a two main ways to use in your project. The first way is to 
just download the Ebanc.php file and require it in your project. The other way is via composer.

####Composer####
You can add this 
to your project via composer by including the following information in your composer.json file:

    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/eBanc/ebanc-php"
        }
    ],
    "require": {
        "ebanc/ebanc-php": "master"
    }

Usage
-----

####Initalize####
You initalize the API client in the following way:

    import 'Ebanc';
    
    $apiKey    = '123456789';
    $gatewayId = 'a01';
    $ebanc = Ebanc.new($apiKey, $gatewayId);


####Customers####

Get a list of all this account's customers

    $customers = $ebanc.getCustomers();

Get a specific customer's details:

    $uuid = '03ae8670-27d3-0132-54de-1040f38cff7c';
    $customer = $ebanc.getCustomer($uuid);

Create a customer and get the uuid:

    $firstName     = 'Steve';
    $lastName      = 'Bobs';
    $routingNumber = '123456789';
    $accountNumber = '123456';
    
    $customer = $ebanc.createCustomer($firstName, $lastName, $routingNumber, $accountNumber);
    $uuid = $customer.uuid;

Update a customer:

    $uuid          = '03ae8670-27d3-0132-54de-1040f38cff7c';
    $firstName     = 'Steve';
    $lastName      = 'Bobs';
    $routingNumber = '123456789';
    $accountNumber = '123456';
    
    $customer = $ebanc.updateCustomer($uuid, $firstName, $lastName, $routingNumber, $accountNumber);


####Transactions####

Get a list of all this account's last 50 transactions

    $transactions = $ebanc.getTransactions();

Get a the latest information about a specific transaction

    $uuid = '03ae8670-27d3-0132-54de-1040f38cff7c';
    $transaction = $ebanc.getTransaction($uuid);

#####Creating Transactions#####
When creating a transaction you can either pass in all customer details or simply pass in 
the uuid for an already created customer. Sometimes it makes sense to just pass in all of 
the details. This is usually in the case of a single transaction. Other times it makes more 
sense to store the customer details and just store that uuid on your server to pass in at payment time. This is a good approch when you will have returning customers or need to setup some kind of a schedule, but don't want to store that sensitive information on your server.

Create Transaction by passing in all details.

    $ebanc.createTransaction('Fred', 'Johnson', '123456789', '123456', '150.92');

######Categories and Memos######
A category and memo can be used together or seperate to help you with reporting later. The category helps group transaction types together (Example: "Online orders" and "In-store 
orders"). The memo helps discribe that specific transaction (Example: Put in the ID number 
of order from your eCommerce or POS system to tie that transaction to the correct order).

Create Transaction by passing in all details and optional category and/or memo:

    $ebanc.createTransaction('Fred', 'Johnson', '123456789', '123456', '150.92', 'Online Orders', 'Order# 1234');

######Customer UUID######
Create Transaction by passing in customer UUID:

    $ebanc.createTransactionForCustomer('03ae8670-27d3-0132-54de-1040f38cff7c', '150.92');

Create Transaction by passing in customer UUID and optional category and/or memo:

    $ebanc.createTransaction('03ae8670-27d3-0132-54de-1040f38cff7c', '150.92', 'Online Orders', 'Order# 1234');