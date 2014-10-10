ebanc-php
=========

PHP bindings for the eBanc API

Installation
------------
When using PHP bindings for the eBanc API, there are a two main ways to use in your project. The first way is to 
just download the Ebanc.php file and require it in your project. The other way is via composer. You can add this 
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