<?php
/**
 * CCSVExport class file.
 *
 * @author Mougrim <rinat@mougrim.ru>
 * @link https://github.com/mougrim/CCSV
 */

/**
 * CCSVExport класс позволяющий делать экспорт в формат CSV
 *
 * @author Mougrim <rinat@mougrim.ru>
 * @package lib.csv
 */
class CCSVExport extends CCSV
{
	/**
	 * @var string $sContentType заголовок Content-type, если значение пустое, то заголовок не выдается
	 */
	private $_contentType = 'text/x-csv';
	protected $csvRows = array();

	/**
	 * @param array $headers
	 * @param array $content
	 */
	public function __construct(array $headers = array(), array $content = array())
	{
		$this->addTableWithHeaders($headers, $content);
	}

	public function addRow($row)
	{
		$this->csvRows[] = $row;
	}

	public function addRowByKeys($keys, $data)
	{
		$row = array();

		foreach($keys as $key)
		{
			$row[] = $data[$key];
		}

		$this->addRow($row);
	}

	public function addTable($table)
	{
		$this->csvRows = array_merge($this->csvRows, $table);
	}

	public function addTableByKeys($keys, $table)
	{
		foreach($table AS $data)
		{
			$this->addRowByKeys($keys, $data);
		}
	}

	public function addTableWithHeaders($headers, $table)
	{
		$this->addRow($headers);
		$this->addTableByKeys(array_keys($headers), $table);
	}

	/**
	 * @param string $string строка, которую нужно обрамить символом $this->enclosed
	 * @return string обрамленная строка
	 */
	protected function prepareStr($string)
	{
		return $this->getEnclosed() . str_replace($this->getEnclosed(), $this->getEscaped() . $this->getEnclosed(), $string) . $this->getEnclosed();
	}

	/**
	 * @return string данные в формате CSV
	 */
	public function toCSV()
	{

		$csvRows = array();

		foreach($this->csvRows as $row)
		{
			$csvRow = array();

			foreach($row as $value)
			{
				$csvRow[] = $this->createField($value);
			}

			$csvRows[] = implode($this->getSeparator(), $csvRow);
		}

		$csv = implode($this->getTerminated(), $csvRows);

		$csv = $this->addBOMHeaders($this->convert($csv));

		return $csv;
	}

	/**
	 * Создание ячейки. Если соддержимое не число, то оно обрамляется функцией $this->prepareStr().
	 *
	 * @param mixed $field содержимое ячейки
	 * @return string созданная ячейка в виде строки
	 */
	protected function createField($field)
	{
		if(!is_float($field) && !is_int($field))
		{
			$field = $this->prepareStr($field);
		}

		return $field;
	}

	/**
	 * Отправление файла CSV в браузер
	 * @param string $fileName название файла
	 */
	public function sendFile($fileName = '')
	{
		$output = $this->toCSV();

		$fileName = $fileName . '_' . date("Y-m-d_H-i", time()) . '.csv';
		header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");

		// Output to browser with appropriate mime type, you choose ;)
		if($this->getContentType() != '')
		{
			header("Content-type: {$this->getContentType()}");
		}

		header("Content-Disposition: attachment; filename={$fileName}");
		echo $output;
	}

	/**
	 * Сохранение CSV в файл
	 *
	 * @param string $pathToFile путь, куда нужно сохранить файл
	 * @param boolean $isOverwrite
	 * @return boolean
	 */
	public function saveAs($pathToFile, $isOverwrite = true)
	{
		if(!$isOverwrite && file_exists($pathToFile))
		{
			return false;
		}

		return (boolean) file_put_contents($pathToFile, $this->toCSV());
	}

	/**
	 * @param string $contentType заголовок Content-type, если значение пустое, то заголовок не выдается
	 */
	public function setContentType($contentType)
	{
		$this->_contentType = $contentType;
	}

	/**
	 * @return string заголовок Content-type
	 */
	public function getContentType()
	{
		return $this->_contentType;
	}
}