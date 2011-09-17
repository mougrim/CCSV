<?php
/**
 * CCSV class file.
 *
 * @author Mougrim <rinat@mougrim.ru>
 * @link https://github.com/mougrim/CCSV
 */

/**
 * CCSV базовый класс для работы с CSV
 *
 * @author Mougrim <rinat@mougrim.ru>
 * @package lib.csv
 */
abstract class CCSV
{
	/**
	 * @var string $sTerminated разделитель строк
	 */
	private $_terminated = "\r\n";
	/**
	 * @var string $sSeparator разделитель ячеек
	 */
	private $_separator = "	";
	/**
	 * @var string $sEnclosed символ обрамления строк с запрещенными символами
	 */
	private $_enclosed = '"';
	/**
	 * @var string $sEscaped символ, который экренирует $this->enclosed
	 */
	private $_escaped = '"';
	/**
	 * @var string $sCharser конечная кодировка
	 */
	private $_charset = 'UCS-2LE';
	/**
	 * 00 00 FE FF UTF-32, big-endian
	 * FF FE 00 00 UTF-32, little-endian
	 * FE FF UTF-16, big-endian
	 * FF FE UTF-16, little-endian
	 * EF BB BF UTF-8
	 *
	 * @var array $hBOMHeaders хеш ([кодировка] => [BOM header])
	 */
	private $_BOMHeaders = array(
		'UCS-2LE'  => "\xFF\xFE",
		'UTF-8'    => "\xEF\xBB\xBF",
		'UTF-16LE' => "\xFF\xFE",
		'UTF-16BE' => "\xFE\xFF",
		'UTF-32LE' => "\xFF\xFE",
		'UTF-32BE' => "\x00\x00\xFE\xFF",
	);

	/**
	 * Конвертирование строки в кодировку $this->charset
	 *
	 * @param string $csv
	 * @return string
	 */
	public function convert($csv)
	{
		if($this->getCharset() == 'UTF-8')
		{
			return $csv;
		}

		$csv = iconv('UTF-8', $this->getCharset(), $csv);

		return $csv;
	}

	/**
	 * Конвертирование строки в кодировку UTF-8 из $this->charset
	 *
	 * @param string $csv
	 * @return string
	 */
	public function unConvert($csv)
	{
		if($this->getCharset() == 'UTF-8')
		{
			return $csv;
		}

		$csv = iconv($this->getCharset(), 'UTF-8', $csv);

		return $csv;
	}

	/**
	 * Добавление BOM-заголовков к строке
	 *
	 * @param string $csv
	 * @return string
	 */
	public function addBOMHeaders($csv)
	{
		if($this->getBOMHeader())
		{
			$csv = $this->getBOMHeader() . $csv;
		}

		return $csv;
	}

	/**
	 * Функция удаляет BOM-заголовки
	 *
	 * @param string $csv
	 * @return string
	 */
	public function removeBOMHeaders($csv)
	{
		$BOMHeader = $this->getBOMHeader();

		if($BOMHeader === null || strlen($BOMHeader) > strlen($csv))
		{
			return $csv;
		}

		if($BOMHeader === substr($csv, 0, strlen($BOMHeader)))
		{
			$csv = substr($csv, strlen($BOMHeader));
		}

		return $csv;
	}

	/**
	 * @return string
	 */
	public function getBOMHeader()
	{
		if(array_key_exists($this->getCharset(), $this->_BOMHeaders))
		{
			return $this->_BOMHeaders[$this->getCharset()];
		}

		return null;
	}

	/**
	 * @param string $charset
	 */
	public function setCharset($charset)
	{
		$this->_charset = $charset;
	}

	/**
	 * @return string
	 */
	public function getCharset()
	{
		return $this->_charset;
	}

	/**
	 * @param string $enclosed
	 */
	public function setEnclosed($enclosed)
	{
		$this->_enclosed = $enclosed;
	}

	/**
	 * @return string
	 */
	public function getEnclosed()
	{
		return $this->_enclosed;
	}

	/**
	 * @param string $escaped
	 */
	public function setEscaped($escaped)
	{
		$this->_escaped = $escaped;
	}

	/**
	 * @return string
	 */
	public function getEscaped()
	{
		return $this->_escaped;
	}

	/**
	 * @param string $separator
	 */
	public function setSeparator($separator)
	{
		$this->_separator = $separator;
	}

	/**
	 * @return string
	 */
	public function getSeparator()
	{
		return $this->_separator;
	}

	/**
	 * @param string $terminated
	 */
	public function setTerminated($terminated)
	{
		$this->_terminated = $terminated;
	}

	/**
	 * @return string
	 */
	public function getTerminated()
	{
		return $this->_terminated;
	}

}