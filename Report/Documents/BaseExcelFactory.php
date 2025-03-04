<?php
namespace SED\Report\Documents;

use SED\Report\Components\FinedUser;
use SED\Report\Components\Link;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

abstract class BaseExcelFactory
{
	protected int $top_indentation = 4;

	public function create(Worksheet $sheet): Worksheet
	{
		$documents = $this->getDocuments();
		$idx = 0;

		foreach ($documents as $document) {

			$reflection = new \ReflectionClass($document);
			$props = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
			$max_rows = 1;

			foreach ($props as $prop) {
				$value = $prop->getValue($document);

				if (is_array($value)) {
					$max_rows = max($max_rows, count($value));
				}
			}

			foreach ($props as $prop) {
				$prop_name = $prop->getName();
				$map = $this->getPropertyMapping();

				if (!isset($map[$prop_name])) {
					throw new \LogicException("Свойства $prop_name нет в массиве сопоставлений!");
				}

				$cell = $map[$prop_name];
				$value = $prop->getValue($document);

				$sheet->duplicateStyle(
					$sheet->getStyle("{$cell}{$this->top_indentation}"),
                    $this->getCellAddress($cell, $idx) . ':' . $this->getCellAddress($cell, ($idx + $max_rows) - 1)
				);


				$this->renderCell($sheet, $value, $cell, $idx, $max_rows);

				$sheet->getColumnDimension($cell)->setAutoSize(true);
			}

			$idx += $max_rows;
		}

		return $sheet;
	}

	abstract protected function getDocuments(): Collection;
	abstract protected function getPropertyMapping(): array;
	private function getCellAddress(string $cell, int $idx, int $increment = 0): string
	{
		return $cell . (($idx + $increment) + $this->top_indentation);
	}

	private function getFirstCell(): string
	{
		$map = $this->getPropertyMapping();
		return $map[array_key_first($map)];
	}

	private function getLastCell(): string
	{
		$map = $this->getPropertyMapping();
		return $map[array_key_last($map)];
	}

	private function renderCell(Worksheet $sheet, $value, $cell, $idx, $max_rows): void
	{
		if (is_array($value)) {
			foreach ($value as $key => $item_value) {
				if ($item_value instanceof FinedUser) {
					$sheet->setCellValue($this->getCellAddress($cell, $idx, $key), $item_value->full_name);
				} else if ($item_value instanceof Link) {
					$cell_address = $this->getCellAddress($cell, $idx, $key);

					$sheet->setCellValue($cell_address, $item_value->title);

					$sheet->getCell($cell_address)->getHyperlink()->setUrl($item_value->url);
				} else {
					$sheet->setCellValue($this->getCellAddress($cell, $idx, $key), $item_value);
				}
			}

			if (count($value) <= 0) {
				$sheet->setCellValue($this->getCellAddress($cell, $idx), '');
			}
		} else if ($value instanceof Link) {
			$cell_address = $this->getCellAddress($cell, $idx);

			$sheet->setCellValue($cell_address, $value->title);

			$sheet->getCell($cell_address)->getHyperlink()->setUrl($value->url);
		} else if ($value instanceof FinedUser) {
			$sheet->setCellValue($this->getCellAddress($cell, $idx), $value->full_name);
		} else if (is_numeric($value)) {
			$sheet->setCellValue($this->getCellAddress($cell, $idx), $value == 0 ? '' : $value);
		} else {
			$sheet->setCellValue($this->getCellAddress($cell, $idx), $value);
		}

		if (!is_array($value)) {
			$sheet->mergeCells(
				$this->getCellAddress($cell, $idx) . ':' . $this->getCellAddress($cell, $idx + $max_rows - 1)
			);
		}
	}
}