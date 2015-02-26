# PHP CoinSimple API

The CoinSimple API allows you to create invoices for your businesses programatically using the Business's ID and API key.

## Installation

#### Composer

You can add this repo to your project via composer, to both download and include the neccessary classes into your project. To do this simply add `coinsimple/coinsimple` to your `composer.json` like in the following:

```json
{
    "require": {
        "coinsimple/coinsimple": "0.0.1"
    }
}
```

#### Manual Install

Alternatively you can download the repo manually and `require` the `coinsimple.php` file into your project.

## Usage

To create a new invoice you need to objects, a `business` and an `invoice`. A business contains the `Business ID` and the `API Key` and handles authorization:

```php
$business = new \CoinSimple\Business(BUSINESS_ID, API_KEY);
```

You can then use this instance to send invoices. An invoice can have the following fields:

```php
$data = array(
    "name" => "",
    "email" => "",
    "items" => array(
        array("description" => "", "price" => 0.0, "quantity" => 0)
    ),
    "processor" => "",
    "rate" => "",
    "currency" => "",
    "notes" => "",
    "percent" => 0.0,
    "custom" => array(),
    "callback_url" => "",
    "redirect_url" => "",
    "invoice_type" => "",
    "interval" => 0,
    "recurring_times" => 0
);

$invoice = new \CoinsSimple\Invoice($data);
```

But the only fields that are really required are `name`, `email`, `items`, `processor`, `rate` and `currency`. If you set defaults for `processor`, `rate` and `currency` in the business settings on CoinSimple, then they can be left out as well.

This library also contains the following functions if you preffer to setup the invoice incrementally:

- `->setName($name)`
- `->setEmail($email)`
- `->addItem($item)`
- `->setProcessor($processor)`
- `->setRate($rate)`
- `->setCurrency($currency)`
- `->setNotes($notes)`
- `->setPercent($percent)` (alias: `->setDiscount($percent)`)
- `->setCustom($data)`
- `->setCallbackUrl($url)`
- `->setRedirectUrl($url)`
- `->setRecurringTimes($times)` (alias: `->stopAfter($times)`)
- `->recurByDays($num_days)`
- `->recurByDate($day_of_month)`

These methods are all chainable and you can mix both options of using functions and setting intial parameters in the constructor. For example you can create an invoice like so:

```php
$user_info = array(
    "name" => "Coin Simple",
    "email" => "hi@coinsimple.com"
);

$invoice = new \CoinSimple\Invoice($user_info);

$invoice.addItem(array(
    "description" => "Nice Shirt",
    "price" => 15.8,
    "quantity" => 2,
))->setCurrency("usd")->recurByDays(4)->stopAfter(2);
```

This code will create an invoice with one item which will recur 2 times once every four days.

To then send the invoice using a business you created you can use the `sendInvoice` method:

```php
$business->sendInvoice($invoice);
```
