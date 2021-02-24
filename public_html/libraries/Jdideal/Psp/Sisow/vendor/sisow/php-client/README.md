# Sisow PHP Library

With this library you can easily connect your custom website/webshop to Sisow.

## Getting Started

These instructions will get you a copy of the project up and running.

### Prerequisites

Your web server needs to support at least the following.

```
PHP 7.0.7 or higher
PHP cURL extension installed
```

### Installing

Installing this package can easily by composer.

```
$ composer require sisow/php-client:^1.0

{
    "require": {
        "sisow/php-client": "^1.0"
    }
}
```

## Getting started

First you need to initialize the Sisow client and set your Merchant ID and Merchant Key. Optional to set your Shop ID.

```
$sisow = new \Sisow\SisowClient();
$sisow->setApiKey('merchantID', 'merchantKey', 'shopID');
```

If you initialized the client you can create your first payment. Below is an example of the request with the required parameters. For all the parameters download our [API documentation](https://www.sisow.nl/developers/).

```
$payment = $sisow->transactions->create([
    "payment" => 'ideal',
    "purchaseid" => 'orderID',
    "amount" => 100, // amount is in cents (100 equals 1,00)
    "description" => 'Webshop Order #', // description for consumer bank statement
    "returnurl" => 'https://mywebshop.com'
]);
```

After creating the payment you can access the payment status by the $payment->status parameter, this parameter can have the value Open, Pending or Reservation. Optional you can save the $payment->transactionId parameter to the database (this parameter is empty when payment equals ideal and no issuerid is set).

```
switch($payment->status){
    case 'Pending':
        // set order state to pending
        break;
    case 'Reservation':
        // set order state to reservation
        break;
    default:
        // status open, send consumer to the issuer URL to complete the payment
        header('Location: ' . $payment->issuerUrl);
        exit;
}
```

If the request for some reason fails a \Sisow\Exceptions\SisowException will be thrown. You can intercept this with a try/catch block. 

### Retrieve payment

If you want to know the actual payment status, you can retrieve the payment.

```
$payment = $sisow->transactions->get('sisowTransactionId');
```

### Refunding payment

All the payment methods except the Giftcards and Pay Later payment methods support the refund API. For the pay later methods you need to use the invoices endpoint.

```
$refundId = $sisow->transactions->refund('sisowTransactionId', 100); // 100 is amount in cents (100 equals 1,00)
```

## Unit Testing

If you want to execute the unit test you need to insert your Merchant ID and Merchant key to the class 'Tests\Sisow\Helpers\SisowClientTestHelper'.

Once added you can execute all the unit test except the AdjustPurchaseIdTest and RefundRequestTest. For these test you need to add the missing information at the top of the function.

## Contributing

We are open for suggestion on our code, if you want to make a contribution you can submit a pull request.

## Versioning

For the versions available, see the [tags on this repository](https://github.com/Sisow/php-client/tags). 

## Authors

* **Mark van Haaren** - *Initial work* - [Sisow B.V.](https://www.sisow.nl)
