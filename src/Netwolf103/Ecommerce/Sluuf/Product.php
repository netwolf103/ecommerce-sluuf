<?php
namespace Netwolf103\Ecommerce\Sluuf;

/**
 * Product class.
 *
 * @author Zhang Zhao <netwolf103@gmail.com>
 */
class Product
{
	/**
	 * Product data
	 *
	 * @var array
	 */
	private $data = [];

	/**
	 * PHPExcel object
	 *
	 * @var \PHPExcel
	 */
	protected $excel;

	/**
	 * Read csv file
	 *
	 * @param string $csvFile
	 */
	function __construct(string $csvFile)
	{
		$csv = array_map('str_getcsv', file($csvFile));

		$data = [];
	    array_walk($csv, function(&$item) use (&$data) {
			$data[$item[0]] = $item[1];
	    });

	    $this->data 	= $data;
	    $this->excel 	= new \PHPExcel();
	}

	/**
	 * Magic method "getXX"
	 *
	 * @param  string $name
	 * @param  array $arguments
	 * @return mixed
	 */
    public function __call($name, $args) 
    {
    	$value = '';

        if (substr($name, 0, 3) == 'get') {
        	$field = ucwords(substr($name, 3));
        	$value = $this->data[$field] ?? '';
        }

        return $value;
    }

    /**
     * Return image urls
     *
     * @return array
     */
    public function getImageUrls(): array
    {
    	$urls = [];

	    array_walk($this->data, function($item, $key) use (&$urls) {
	    	if (strstr($key, 'Image')) {
	    		$urls[] = $item;
	    	}
	    });

	    return $urls; 	
    }

    /**
     * Return special price
     *
     * @param  float $multiple
     * @param  float $suffix
     * @param  float $minPrice
     * @return float
     */
    public function getSpecialPrice(float $multiple = 5.0, float $suffix = 0.95, float $minPrice = 100.0): float
    {
    	$price = $this->data['Price'] ?? '';
    	$price = explode(' ', $price);
    	$price = $price[1] ?? 0;
    	$price = $price * $multiple;

    	if ($price <= 0) {
    		$price = $minPrice;
    	}

    	return floor($price) + $suffix;
    }

    /**
     * Return price
     *
     * @param  float  $multiple
     * @param  float  $suffix
     * @return float
     */
    public function getPrice(float $multiple = 2.2, float $suffix = 0.95): float
    {
    	$specialPrice = $this->getSpecialPrice();

    	return floor($specialPrice * $multiple) + $suffix;
    }

    /**
     * Return stock
     *
     * @param  int|integer $stock
     * @return int
     */
    public function getStock(int $stock = 10): int
    {
    	return $stock;
    }

    /**
     * Return shipping fee
     *
     * @param  float  $price
     * @return float
     */
    public function getShipFee(float $price = 20.0): float
    {
    	return $price;
    }

    /**
     * Return color
     *
     * @param  string $color
     * @return string
     */
    public function getColor(string $color = 'White'): string
    {
    	return $color;
    }

    /**
     * Return description
     *
     * @return
     */
    public function getDesc(): string
    {
    	$desc = $this->data['Desc'] ?? null;
    	
    	if (!$desc) {
    		$config = $this->loadConfig();
    		$desc = $config['Desc'] ?? '';
    	}

    	return $desc;
    }

    /**
     * Create a excel file
     *
     * @param  string $filename
     * @return self
     */
    public function saveExcel(string $filename)
    {
    	$docTitle = sprintf('Sluuf Product - %s', $this->getSku());

		$this->excel
			->getProperties()
			->setTitle($docTitle)
			->setSubject($docTitle)
			->setDescription($docTitle)
			->setKeywords($docTitle)
			->setCategory($docTitle);

	    array_walk($this->getHeader(), function($item, $cell) {
			$this->excel
				->getActiveSheet()
				->setCellValue($cell.'1', $item)
			;
	    });

		return $this;
    }

    /**
     * Return excel header
     *
     * @return array
     */
    protected function getHeader(): array
    {
    	$header = [
    		'A' => '*父SKU(parentSku)',
    		'B' => '*子SKU(sku)',
    		'C' => '*产品名称(title)',
    		'D' => '颜色(color)',
    		'E' => '尺寸(size)',
    		'F' => '*产品数量(quantity)',
    		'G' => '*标签{用英文逗号[,]隔开}(tags)',
    		'H' => '*描述(description)',
    		'I' => 'MSRP原价[$](msrp)',
    		'J' => '*售价[$](price)',
    		'K' => '*运费[$](shipping)',
    		'L' => '*运输时间[天](shippingTime)',
    		'M' => '*产品主图链接(mainImage)',
    		'N' => 'SKU图片链接(skuImage)',
    	];

		$imageUrls = $this->getImageUrls();
		array_shift($imageUrls);

		$cellsImage = range('O', 'Z');
		$index 		= 1;

		foreach($imageUrls as $cell => $imageUrl) {
			$header[$cellsImage[$cell]] = sprintf('附图链接%s(image%s)', $index, $index);
			$index++;
		}    	

    	return $header;
    }

    /**
     * Return config
     *
     * @return array
     */
    protected function loadConfig(): array
    {
    	$config = dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/config.csv';

    	$data = [];

    	if (file_exists($config)) {
			$csv = array_map('str_getcsv', file($config));

			$data = [];
		    array_walk($csv, function(&$item) use (&$data) {
				$data[$item[0]] = $item[1];
		    });
    	}

    	return $data;
    }    
}