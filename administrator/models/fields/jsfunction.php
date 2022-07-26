<?php
/**
 * @package		com_tjfields
 * @version		$versionID$
 * @author		TechJoomla
 * @author mail	extensions@techjoomla.com
 * @website		http://techjoomla.com
 * @copyright	Copyright © 2009-2013 TechJoomla. All rights reserved.
 * @license		GNU General Public License version 2, or later
*/

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Language\Text;

/**
 * Supports an HTML select list of categories
 */
class JFormFieldJsfunction extends FormField
{
	protected $type = 'text';

	function __construct ()
	{
		parent::__construct();
		$this->countoption        = 0;
		$this->tjfield_icon_plus  = "icon-plus-2 ";
		$this->tjfield_icon_minus = "icon-minus-2 ";
	}

	protected function getInput()
	{
		$jsarray                       = explode('||', $this->value);
		$jsarray_removed_blank_element = array_filter($jsarray);
		$countjs                       = empty($this->value) ? 0 : count($this->value);

		if(empty($this->value))
		$countjs = 0;
			$j=0;
			$html='';

			Factory::getDocument()->addScriptDeclaration("
				var js_lenght = '" . $countjs ."';
				var tjfield_icon_minus = 'icon-minus-2 ';
			");

			$html.='<div class="techjoomla-bootstrap">
				<div id="tjfield_js_container" class="tjfield_js_container" >';

			if ($this->value)
			{
				for ($j=0;$j<=count($jsarray_removed_blank_element);$j++)
				{
					$jsarray_final = '';

					if ($j < count($jsarray_removed_blank_element))
					{
						$jsarray_final = explode('-', $jsarray_removed_blank_element[$j]);
					}

					$html.=	'<div id="com_tjfields_js__repeating_block'.$j.'"    class="com_tjfields_js__repeating_block span9">
								<div class="form-inline">
									'.$this->fetchJsfunction($this->name,($jsarray_final[0])?$jsarray_final[0]:"", $this->element, $this->options['control'],$j).$this->fetchJsfunctionName($this->name,($jsarray_final[1])?$jsarray_final[1]:"", $this->element, $this->options['control'],$j).'
								</div>
							</div>';

					if ($j<count($this->value))
					{
						$html.='<div id="remove_btn_js__div'.$j.'" class="com_tjfields_remove_button span2">
									<div class="com_tjfields_remove_button">
										<button class="btn btn-small btn-danger" type="button" id="remove_js'.$j.'" onclick="removeClone(\'com_tjfields_js__repeating_block'.$j.'\',\'remove_btn_js__div'.$j.'\');" >
											<i class="'.$this->tjfield_icon_minus.'"></i>
										</button>
									</div>
								</div>';
					}
				}
			}
			else
			{
				$html.=	'<div id="com_tjfields_js__repeating_block0" class="com_tjfields_js__repeating_block span9">
							<div class="form-inline">
								'.$this->fetchJsfunction($this->name,(isset($this->value[$j]->options))?$this->value[$j]->options:"", $this->element, isset($this->options['control']),$j).$this->fetchJsfunctionName($this->name,(isset($this->value[$j]->value))?$this->value[$j]->value:"", $this->element, isset($this->options['control']),$j).'
							</div>
						</div>';
			}

			$html.='<div class="com_tjfields_add_button span2">
						<button class="btn btn-small btn-success" type="button" id="add_js"
						onclick="addClonejsOption(\'com_tjfields_js__repeating_block\',\'com_tjfields_js__repeating_block\');"
						title='.Text::_("COM_TJFIELDS_ADD_BUTTON").'>
							<i class="'.$this->tjfield_icon_plus.'"></i>
						</button>
					</div>';

			if (JVERSION < '4.0.0')
			{
				$html.= '<div style="clear:both"></div>
				<span class="span9 alert alert-info alert-help-inline">
						'.Text::_("COM_TJFIELDS_JS_NOTE").'
				</span>';
			}
			else
			{
				$html.= '<div class="control-label">&nbsp;</div>
				<div class="controls alert alert-info alert-help-inline">
						'.Text::_("COM_TJFIELDS_JS_NOTE").'
				</div>';
			}

		$html.= '</div>

			</div>';//bootstrap div

			return $html;
	}

	var	$_name = 'jsfunction';

	function fetchJsfunction($fieldName, $value, &$node, $control_name,$j)
	{
		return $Jsfunction = '<input type="text" id="tjfields_jsoptions_'.$j.'" name="tjfieldsJs['.$j.'][jsoptions]"  class="tjfields_jsoptions form-control"  placeholder="Events"  value="'.$value.'">';
	}

	function fetchJsfunctionName($fieldName, $value, &$node, $control_name,$j)
	{
		return $JsfunctionName = '<input type="text" id="tjfields_jsfunctionname_'.$j.'" name="tjfieldsJs['.$j.'][jsfunctionname]"  class="tjfields_jsfunctionname form-control"  placeholder="Function name"  value="'.$value.'">';
	}
}
?>
<script type="text/javascript">
	function addClonejsOption(rId,rClass)
	{
		var pre=js_lenght;
		js_lenght++;

		var removeButton="<div id='remove_btn_js__div"+pre+"' class='com_tjfields_remove_button span2'>";
		removeButton+="<button class='btn btn-small btn-danger' type='button' id='remove_js"+pre+"'";
		removeButton+="onclick=\"removeClone('com_tjfields_js__repeating_block"+pre+"','remove_btn_js__div"+pre+"');\" title=\"<?php echo Text::_('COM_TJFIELDS_REMOVE_TOOLTIP');?>\" >";
		removeButton+="<i class=\""+tjfield_icon_minus+"\"></i></button>";
		removeButton+="</div>";

		var newElem=techjoomla.jQuery('#'+rId+pre).clone().attr('id',rId+js_lenght);
		newElem.find('input[name=\"tjfieldsJs[' + pre + '][jsoptions]\"]').attr({'name': 'tjfieldsJs[' + js_lenght + '][jsoptions]','value':''});
		newElem.find('input[name=\"tjfieldsJs[' + pre + '][jsfunctionname]\"]').attr({'name': 'tjfieldsJs[' + js_lenght + '][jsfunctionname]','value':''});

		/*incremnt id*/
		newElem.find('input[id=\"tjfields_jsoptions_'+pre+'\"]').attr({'id': 'tjfields_jsoptions_'+js_lenght,'value':''});
		newElem.find('input[id=\"tjfields_jsfunctionname_'+pre+'\"]').attr({'id': 'tjfields_jsfunctionname_'+js_lenght,'value':''});

		techjoomla.jQuery('#'+rId+pre).after(newElem);
		techjoomla.jQuery('#'+rId+pre).after(removeButton)
	}

	function removeClone(rId,r_btndivId)
	{
		techjoomla.jQuery('#'+rId).remove();
		techjoomla.jQuery('#'+r_btndivId).remove();
	}
</script>
