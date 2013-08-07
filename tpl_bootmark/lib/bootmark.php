<?php
/**
 * @copyright   Copyright (C) 2013 mktgexperts.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see http://www.gnu.org/licenses/gpl-2.0.html
 */



// no direct access
defined('_JEXEC') or die;

// jimport( 'joomla.registry.registry' );

class bootmarkFramework {
	/**
	 * The document
	 *
	 * @var    JDocumentHTML
	 */
	protected $doc;

	/**
	 * Template positions
	 *
	 * @var    array
	 */
	protected $positions;

	/**
	 * Layout settings
	 *
	 * @var    JRegistry
	 */
	protected $positionsSettings;

	/**
	 * Template params
	 *
	 * @var    JRegistry
	 */
	protected $params;

	/**
	 * Instantiate the class.
	 *
	 * @param   JDocumentHTML  $_template  Current Document.
	 */
	public function __construct(&$doc){
		// declare and collect data
		$this->doc = $doc;
		$this->positions = $this->getPositions(0, $doc->template);
		$this->params = JFactory::getApplication()->getTemplate(true)->params;
		$this->positionsSettings = new JRegistry;
		if ($this->params->get("positions_settings")){
			$this->positionsSettings->loadString((string)$this->params->get("positions_settings"));
		}
		// check errors
			// check if component position exits at the manifest file
			// check if sidebar-[a-c] positions exits at the manifest file
			// check if compile folder have permissions problems

		// compile LESS files
		echo "";
	}

	/**
	 * Return true if at least one module exist in the matched positions.
	 *
	 * @param   string  $exp	regular expression to match a list of positions.
	 *
	 * @return  boolean.
	 */
	public function hasModules($exp){
		// filter positions to evaluate
		preg_match_all("/$exp/i", implode(" ", $this->positions), $positions);
		foreach ($positions[0] as $position) {
			if ($this->doc->countModules($position)) return true;
		}
		return false;
	}

	/**
	 * Return a list of template positions.
	 *
	 * @param   integer  $clientId  client id number (1 = administrator, 0 = site).
	 * @param   string  $templateDir  template folder name.
	 *
	 * @return  boolean.
	 */
	function getPositions($clientId, $templateDir) {
		$positions = array();

		$templateBaseDir = $clientId ? JPATH_ADMINISTRATOR : JPATH_SITE;
		$filePath = JPath::clean($templateBaseDir . '/templates/' . $templateDir . '/templateDetails.xml');

		if (is_file($filePath)){
			// Read the file to see if it's a valid component XML file
			$xml = simplexml_load_file($filePath);
			if (!$xml){
				return false;
			}

			// Check for a valid XML root tag.
			// Extensions use 'extension' as the root tag.  Languages use 'metafile' instead

			if ($xml->getName() != 'extension' && $xml->getName() != 'metafile'){
				unset($xml);
				return false;
			}

			$positions = (array) $xml->positions;

			if (isset($positions['position'])){
				$positions = $positions['position'];
			}else{
				$positions = array();
			}
		}

		return $positions;
	}

	/**
	 * Return the biggest level index of the matched list of positions.
	 *
	 * @param   string	$group  group name to match elements of the list of positions.
	 *
	 * @return  integer	The level max index value, return false if no matches found.
	 */
	function getGroupMaxLevel($group) {
		// filter positions to evaluate
		preg_match_all("/$exp-\d+/i", implode(" ", $this->positions), $positions);
		if (!count($positions[0])) return false;
		$max = 0;
		foreach ($positions[0] as $position) {
			$level = explode("-",$position);
			if ($level[1] > $max) $max = $level[1];
		}
		return $max;
	}

	/**
	 * Return the markup and template tags for module positions
	 *
	 * @param   string	$group  group name to match elements on the list of positions.
	 * @param   string	$level  level index to match elements on the list of positions.
	 * @param   string	$colum  colum index to match elements on the list of positions.
	 * @param   array	$styles  array of strings to set the chrome styles for the module positions.
	 *
	 * @return  string Markup of the set of matched positions, return false if no matches found.
	 */
	function displayModules($group, $Level, $colum, $styles = "") {
		// filter positions to use
		preg_match_all("/$group-$Level-$colum/i", implode(" ", $this->positions), $positions);
		if (!count($positions[0])) return false;
		$force = $this->positionsSettings->get("force-$group-$Level", 0);
		$count = $this->positionsSettings->get("count-$group-$Level", 0);
		$buffer = array();
		// create array of positions settings
		for ($i = 1; $i <= $count; $i++) {
			$position = $positions[0][$i-1];
			$size = $this->positionsSettings->get("size-$position", 0);
			$buffer[$i]["position"] = $position;
			$buffer[$i]["size"] = $size;
			$buffer[$i]["style"] = isset($styles[$i-1]) ? $styles[$i-1] : "xhtml";
		}
		// compose positions markup
		for ($i = 1; $i <= $count; $i++) {
			$position = $buffer[$i]["position"];
			$size = $buffer[$i]["size"];
			$style = $buffer[$i]["style"];
			if (!$force && !$this->doc->countModules($position)) {
				$buffer[$i]["output"] = "";
			} elseif ($force && !$this->doc->countModules($position)) {
				$buffer[$i]["output"] = "<div class=\"span-$size\">&nbsp;</div>";
			} else {
				$buffer[$i]["output"] = "
					<div class=\"span-$size\">
						<div class=\"block\">
							<jdoc:include type=\"modules\" name=\"$position\" style=\"$style\" />
						</div>
					</div>
				";
			}
		}
		// render positions markup
		foreach ($buffer as $val) {
			echo $val["output"];
		}
	}

	/**
	 * Return the markup and template positions tags for the component and sidebar modules
	 *
	 * @param   array	$styles  array of strings to set the chrome styles for the module positions.
	 *
	 * @return  string Markup of the set of matched positions, return false if no matches found.
	 */
	function displayMainBody($styles = "") {
		$buffer = array();
		$force = $this->positionsSettings->get("force-mainbody", 0);
		$count = $this->positionsSettings->get("count-mainbody", 2);
		// create array of positions settings
		// component
		$size = $this->positionsSettings->get("size-component", 0);
		$order = $this->positionsSettings->get("order-component", 1);
		$buffer[1]["position"] = "component";
		$buffer[1]["size"] = $size;
		// sidebar modules
		$group = "sidebar";
		if ($this->hasModules("$group-[a-c]")) {
			preg_match_all("/$group-[a-c]/i", implode(" ", $this->positions), $positions);
			for ($i = 1; $i <= $count-1; $i++) {
				$position = $positions[0][$i-1];
				$size = $this->positionsSettings->get("size-$position", 0);
				$buffer[$i+1]["position"] = $position;
				$buffer[$i+1]["size"] = $size;
			}
		}
		// compose positions markup
		// component
		$size = $buffer[1]["size"];
		$buffer[1]["output"] = "
				<div class=\"span-$size\">
					<div class=\"block\">
						<jdoc:include type=\"component\" />
					</div>
				</div>
			";
		// modules
		for ($i = 2; $i <= $count; $i++) {
			$position = $buffer[$i]["position"];
			$size = $buffer[$i]["size"];
			if (!$force && !$this->doc->countModules($position)) {
				$buffer[$i]["output"] = "";
			} elseif ($force && !$this->doc->countModules($position)) {
				$buffer[$i]["output"] = "<div class=\"span-$size\">&nbsp;</div>";
			} else {
				$buffer[$i]["output"] = "
					<div class=\"span-$size\">
						<div class=\"block\">
							<jdoc:include type=\"modules\" name=\"$position\" style=\"$style\" />
						</div>
					</div>
				";
			}
		}
		// render positions markup
		foreach ($buffer as $val) {
			echo $val["output"];
		}
	}
}



