<?php
/**
 * @version    SVN: <svn_id>
 * @package    Tjfields
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

// No direct access
defined('_JEXEC') or die();
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

jimport('joomla.application.component.view');

/**
 * View class for editing condition.
 *
 * @package     Tjfields
 * @subpackage  com_tjfields
 * @since       2.2
 */
class TjfieldsViewCondition extends HtmlView
{
	protected $state;

	protected $item;

	protected $form;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	public function display ($tpl = null)
	{
		$this->state = $this->get('State');
		$this->item = $this->get('Item');
		$this->form = $this->get('Form');
		$this->input = Factory::getApplication()->input;

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		$this->addToolbar();

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		Factory::getApplication()->input->set('hidemainmenu', true);

		$user = Factory::getUser();
		$isNew = ($this->item->id == 0);

		// Let's get the extension name
		$client = Factory::getApplication()->input->get('client', '', 'STRING');
		$extensionName = strtoupper($client);

		// Need to load the menu language file as mod_menu hasn't been loaded yet.
		$lang = Factory::getLanguage();
		$lang->load($client, JPATH_ADMINISTRATOR, null, false, true);

		$viewTitle = Text::_($extensionName);

		if ($isNew)
		{
			$viewTitle = $viewTitle . ': ' . Text::_('COM_TJFIELDS_ADD_CONDITION');
		}
		else
		{
			$viewTitle = $viewTitle . ': ' . Text::_('COM_TJFIELDS_EDIT_CONDITION');
		}

		if (JVERSION >= '3.0')
		{
			JToolBarHelper::title($viewTitle, 'pencil-2');
		}
		else
		{
			JToolBarHelper::title($viewTitle, 'condition.png');
		}

		if (isset($this->item->checked_out))
		{
			$checkedOut = ! ($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		}
		else
		{
			$checkedOut = false;
		}

		$extention = explode('.', $client);

		$canDo = TjfieldsHelper::getActions($extention[0], 'condition');

		// If not checked out, can save the item.
		if (! $checkedOut && ($canDo->get('core.edit') || ($canDo->get('core.create'))))
		{
			JToolBarHelper::apply('condition.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('condition.save', 'JTOOLBAR_SAVE');
		}

		if (empty($this->item->id))
		{
			JToolBarHelper::cancel('condition.cancel', 'JTOOLBAR_CANCEL');
		}
		else
		{
			JToolBarHelper::cancel('condition.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}
