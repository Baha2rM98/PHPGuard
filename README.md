PHPGuard
=======
A security cryptography library for PHP. It can be used by different frameworks or pure PHP

PHPGuard Console App
--------------------
A command line interface designed for this library due to set a encryption key, test system etc.

Some of commands:
* [php guard set:key]
* [php guard fresh]
Installation
------------
Use [Composer] to install the package:
```
$ composer require baha2rmirzazadeh/phpguard
```
Examples
-------
```php
use PHPGuard\Crypto\Crypto;
use PHPGuard\Crypto\Key;

$cr = new Crypto("CAST5-CBC");
$cr->setKey(Key::getKey());
$c = $cr->encrypt([
        "Name"      => "Baha2r",
        "LastName"  => "Mirzazadeh",
        "Age"       => 22,
        "IsStudent" => true,
        "Courses"   => ["Math", "Ecocnomy", "Chemistry"]
]);
print $c."\n";
print_r($cr->decrypt($c));


$cr = $cr->setCipher("AES-192-CBC");
$cr->setKey(Crypto::generateKey());
$c = $cr->encrypt([
        "Name"      => "Baha2r",
        "LastName"  => "Mirzazadeh",
        "Age"       => 22,
        "IsStudent" => true,
        "Courses"   => ["Math", "Ecocnomy", "Chemistry"]
]);
print $c."\n";
print_r($cr->decrypt($c));
print_r(Crypto::supported());
```
Author
-------
* [Bahador Mirzazadeh]
* E-Mail: [baha2r.mirzazadeh98@gmail.com]

License
-------
MIT  