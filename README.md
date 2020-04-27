# netwolf103/ecommerce-sluuf
Create product excel file of upload sluuf

# Code maintainers
![头像](https://avatars3.githubusercontent.com/u/1772352?s=100&v=4)
------------
Zhang Zhao <netwolf103@gmail.com>
------------
Wechat: netwolf103

## Require
	"php": "^7.0",
	"phpoffice/phpexcel": "1.8.2"

## Install
composer require netwolf103/ecommerce-sluuf

## Usage
```PHP
require_once "vendor/autoload.php";

use Netwolf103\Ecommerce\Sluuf\Product\Rings;

$dataFile = sprintf('%s/var/1688/demo.csv', dirname(dirname(__FILE__)));
$product = new Rings($dataFile);

$filename = sprintf('%s.xlsx', $product->getSku());
$product->saveExcel($filename);
```