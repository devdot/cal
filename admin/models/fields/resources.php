<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_categories
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');
JLoader::register('CalHelperResources', JPATH_COMPONENT . '/helpers/resources.php');

/**
 * Form Field class for the Joomla Framework.
 *
 * @since  1.6
 */
class JFormFieldResources extends JFormFieldList
{

	/**
	 * Copy-pasta from categoryedit.php (com_categories)
	 *
	 * @var    string
	 * @since  1.6
	 */
	public $type = 'Resources';


	/**
	 * Method to get a list of resource associations
	 * Use the parent element to indicate that the field will be used for assigning parent categories.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   1.6
	 */
	protected function getOptions() {
		$event_id = $this->form->getData()->get('id');
		
		$options = array();


		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('a.resource_id, b.name, b.type')
			->from('#__cal_events_resources AS a')
			->leftJoin('#__cal_resources AS b ON a.resource_id = b.id')
			->where('a.event_id = '.(int)$event_id)
			->order('type ASC, name ASC');

		// Get the options.
		$db->setQuery($query);

		try {
			$options = $db->loadObjectList();
		}
		catch (RuntimeException $e) {
			JError::raiseWarning(500, $e->getMessage());
		}
		
		return $options;
	}
	
	/**
	 * Method for markup. Heavy customized so my stuff actually works.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput() {
		$html = array();
		$html[] = $this->form->getInput('resources_selector');
		return implode($html);
	}
	
	public function renderField($options = array()) {
		/*if ($this->hidden) {
			return $this->getInput();
		}

		if (!isset($options['class'])) {
			$options['class'] = '';
		}

		$options['rel'] = '';

		if (empty($options['hiddenLabel']) && $this->getAttribute('hiddenLabel')) {
			$options['hiddenLabel'] = true;
		}

		if ($showonstring = $this->getAttribute('showon')) {
			$showonarr = array();

			foreach (preg_split('%\[AND\]|\[OR\]%', $showonstring) as $showonfield) {
				$showon   = explode(':', $showonfield, 2);
				$showonarr[] = array(
					'field'  => str_replace('[]', '', $this->getName($showon[0])),
					'values' => explode(',', $showon[1]),
					'op'     => (preg_match('%\[(AND|OR)\]' . $showonfield . '%', $showonstring, $matches)) ? $matches[1] : ''
				);
			}

			$options['rel'] = ' data-showon=\'' . json_encode($showonarr) . '\'';
			$options['showonEnabled'] = true;
		}

		$data = array(
			'input'   => $this->getInput(),
			'label'   => $this->getLabel(),
			'options' => $options
		);*/

		//return $this->getRenderer($this->renderLayout)->render($data);
		//let's just render it all ourselves
		$name = $this->element->attributes()->name->__toString();
		$resources = $this->getOptions();
		
		$res = array();
		foreach($resources as $r) {
			$res[] = '['.$r->resource_id.',"'.CalHelperResources::type($r->type).'","'.$r->name.'"]';
		}
		$res = implode(',',$res);
		
		//first, the upper input group
		$str = '<div class="input-append" id="cal_resources_input_group">';
		$str .= $this->getInput();
		$str .= '<span class="input-group-btn"><button class="btn btn-primary" type="button" onclick="cal_resources_add();">'.JText::_('COM_CAL_ADD').'</button></span></div>';
		
		$str .= '<table class="table table-striped cal-resources-table">';
		$str .= '<thead><tr><th>'.JText::_('COM_CAL_TYPE').'</th><th>'.JText::_('COM_CAL_NAME').'</th><th width="1%"></th></tr><thead>';
		$str .= '<tbody id="cal_resources_table"></tbody></table>';
		
		$str .= '<input type="hidden" name="jform['.$name.']" id="cal_resources_input">';
		
		$str .= '<script>
				var cal_resources = ['.$res.'];
				function cal_resources_add() {
					id = document.getElementById("cal_resources_input_group").getElementsByTagName("select")[0].value;
					if(id == "")
						return false;
					id = parseInt(id);
					
					for(i in cal_resources) {
						if(cal_resources[i][0] == id)
							return false;
					}
					//not already in the list
					
					label = "";
					options = document.getElementById("cal_resources_input_group").getElementsByTagName("option");
					for(i in options) {
						if(options[i].value == id) {
							label = options[i].innerHTML;
							break;
						}
					}
					split = label.split(": "); //this might break when the options get filled differently (more elegant would be nice)
					cal_resources.push([id, split[0], split[1]]);

					cal_resources_save();
					cal_resources_show();
				}
				function cal_resources_delete(index) {
					cal_resources.splice(index, 1);
					
					cal_resources_save();
					cal_resources_show();
				}
				function cal_resources_save() {
					
				}
				function cal_resources_reload() {
				
					cal_resources_show();
				}
				function cal_resources_show() {
					str = "";
					r = [];
					for(i in cal_resources) {
						res = cal_resources[i];
						str += "<tr><td>"+res[1]+"</td><td>"+res[2]+"</td>";
						str += "<td><a href=\"#\"><span class=\"icon-delete\" onclick=\"cal_resources_delete("+i+");\"></span></a></td></tr>";
						r[i] = res[0];
					}
					document.getElementById("cal_resources_table").innerHTML = str;
					document.getElementById("cal_resources_input").value = JSON.stringify(r);
				}
				cal_resources_show();
				</script>';
		return $str;
	}
}
