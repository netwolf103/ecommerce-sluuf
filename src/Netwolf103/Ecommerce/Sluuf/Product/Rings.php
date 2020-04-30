<?php
namespace Netwolf103\Ecommerce\Sluuf\Product;

use Netwolf103\Ecommerce\Sluuf\Product;

/**
 * Rings class.
 *
 * @author Zhang Zhao <netwolf103@gmail.com>
 */
class Rings extends Product
{
    /**
     * Return sizes
     *
     * @param  float  $min
     * @param  float  $max
     * @param  float  $step
     * @return array
     */
    public function getSizes(float $min = 4.0, float $max = 12.0, float $step = 0.5): array
    {
    	return range($min, $max, $step);
    }

    /**
     * Create a excel file
     *
     * @param  string $filename
     * @return self
     */
    public function saveExcel(string $filename)
    {
    	parent::saveExcel($filename);

		$imageUrls 		= $this->getImageUrls();
		$mainImageUrl 	= array_shift($imageUrls);
		$cellsImage 	= range('O', 'Z');

		$row = 2;
		foreach($this->getSizes() as $size) {
			$this->excel
				->getActiveSheet()
				->setCellValue('A'.$row, $this->getSku())
				->setCellValue('B'.$row, sprintf('%s_White_%s', $this->getSku(), $size))
				->setCellValue('C'.$row, $this->getName())
				->setCellValue('D'.$row, $this->getColor())
				->setCellValue('E'.$row, $size)
				->setCellValue('F'.$row, $this->getStock())
				->setCellValue('G'.$row, '')
				->setCellValue('H'.$row, $this->getDesc())
				->setCellValue('I'.$row, $this->getPrice())
				->setCellValue('J'.$row, $this->getSpecialPrice())
				->setCellValue('K'.$row, $this->getShipFee())
				->setCellValue('L'.$row, '')
				->setCellValue('M'.$row, $mainImageUrl)
				->setCellValue('N'.$row, '')
			;

			foreach($imageUrls as $j => $imageUrl) {
				$cell = $cellsImage[$j] ?? '';
				if (!$cell) {
					continue;
				}
				
				$this->excel
					->getActiveSheet()
					->setCellValue($cell.$row, $imageUrl)
				;
			}

			$row++;
		}	

		$objWriter = \PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
		$objWriter->save($filename);

		return $this;
    }    
}