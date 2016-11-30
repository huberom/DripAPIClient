# DripAPIClient
Lightweight object-oriented PHP (JSON) client for DRIP Email Marketing API


## Basic Usage

```php
<?php
    use HOMC/DripClient;

    $client = new DripClient(DRIP_API_KEY);

    $client->addTag([
        'account_id' => 9001,
        'email'      => 'huberom@hotmail.com',
        'tag'        => 'Test tag',
    ]);

?>

```