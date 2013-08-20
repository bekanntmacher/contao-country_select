<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2011 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  bekanntmacher 2012
 * @author     Simon Wohler <http://www.bekanntmacher.ch>
 * @package    Country Select Menu
 * @license    GNU GPL
 * @filesource
 */


/**
 * Class FormCountrySelect
 *
 * Form field "contry select menu".
 * @copyright  bekanntmacher 2011
 * @author     Simon Wohler <http://www.bekanntmacher.ch>
 * @package    Controller
 */
class FormCountrySelect extends Widget
{

	/**
	 * Submit user input
	 * @var boolean
	 */
	protected $blnSubmitInput = true;

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'form_widget';

	/**
	 * Options
	 * @var array
	 */
	protected $arrOptions = array();

	

	/**
	 * Add specific attributes
	 * @param string
	 * @param mixed
	 */
	public function __set($strKey, $varValue)
	{
		switch ($strKey)
		{
			case 'mSize':
				if ($this->multiple)
				{
					$this->arrAttributes['size'] = $varValue;
				}
				break;

			case 'options':
				$this->arrOptions = $this->getCountries($varValue);
				break;

			case 'multiple':
				if (strlen($varValue))
				{
					$this->arrAttributes[$strKey] = 'multiple';
				}
				break;

			case 'mandatory':
				$this->arrConfiguration['mandatory'] = $varValue ? true : false;
				break;

			case 'rgxp':
				break;

			default:
				parent::__set($strKey, $varValue);
				break;
		}
	}
	
	protected function getCountries($varValue=null)
	{
		$return = array();
		$countries = array();
		$arrAux = array();
		$this->loadLanguageFile('countries');
		include(TL_ROOT . '/system/config/countries.php');


		foreach ($countries as $strKey=>$strName)
		{
			$arrAux[$strKey] = strlen($GLOBALS['TL_LANG']['CNT'][$strKey]) ? utf8_romanize($GLOBALS['TL_LANG']['CNT'][$strKey]) : $strName;
		}

		asort($arrAux);
	
		foreach (array_keys($arrAux) as $strKey)
		{
			$return[$strKey] = strlen($GLOBALS['TL_LANG']['CNT'][$strKey]) ? $GLOBALS['TL_LANG']['CNT'][$strKey] : $countries[$strKey];
			
		}
			
		$this->$arrOptions = $varValue;
			
		return $return;
	}
	
	

	/**
	 * Check options if the field is mandatory
	 */
	public function validate()
	{
		$mandatory = $this->mandatory;
		$options = deserialize($this->getPost($this->strName));

		// Check if there is at least one value
		if ($mandatory && is_array($options))
		{
			foreach ($options as $option)
			{
				if (strlen($option))
				{
					$this->mandatory = false;
					break;
				}
			}
		}

		$varInput = $this->validator($options);

		// Add class "error"
		if ($this->hasErrors())
		{
			$this->class = 'error';
		}
		else
		{
			$this->varValue = $varInput;
		}

		// Reset the property
		if ($mandatory)
		{
			$this->mandatory = true;
		}
	}


	/**
	 * Return a parameter
	 * @return string
	 * @throws Exception
	 */
	public function __get($strKey)
	{
		switch ($strKey)
		{
			case 'options':
				print $strKey;
				return $this->arrOptions;
				break;

			default:
				return parent::__get($strKey);
				break;
		}
	}


	/**
	 * Generate the widget and return it as string
	 * @return string
	 */
	public function generate()
	{
		$strOptions = '<option value="">-</option>';
		$strClass = 'select';
		$blnHasGroups = false;

		if ($this->multiple)
		{
			$this->strName .= '[]';
			$strClass = 'multiselect';
		}

		// Make sure there are no multiple options in single mode
		elseif (is_array($this->varValue))
		{
			$this->varValue = $this->varValue[0];
		}

		foreach ($this->arrOptions as $key=>$label)
		{
			
			if($this->country_select_value == 0)
			{
				$value = $key;
			}
			else
			{
				$value = $label;
			}
			
			$arrOption = array('value' => $value, 'label' => $label);
			
			$strOptions .= sprintf('<option value="%s" %s >%s</option>',
									$value,
									$this->isSelected($arrOption),
									$label);
		}

		return sprintf('<select name="%s" id="ctrl_%s" class="%s%s"%s>%s</select>',
						$this->strName,
						$this->strId,
						$strClass,
						(strlen($this->strClass) ? ' ' . $this->strClass : ''),
						$this->getAttributes(),
						$strOptions) . $this->addSubmit();
	}
}

?>