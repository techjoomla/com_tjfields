<?php
/**
 * @version     1.0.0
 * @package     com_tjfields
 * @copyright   Copyright (C) 2014. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      TechJoomla <extensions@techjoomla.com> - www.techjoomla.com
 */

// no direct access
defined('_JEXEC') or die;

use \Joomla\CMS\Layout\LayoutHelper;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
if(JVERSION >= '3.0')
{
	JHtml::_('bootstrap.tooltip');
	JHtml::_('formbehavior.chosen', 'select');
	JHtml::_('behavior.multiselect');
}

//JHtml::_('behavior.multiselect');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_tjfields/assets/css/tjfields.css');

$user	= JFactory::getUser();
$userId	= $user->get('id');
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$canOrder	= $user->authorise('core.edit.state', 'com_tjfields');
$saveOrder	= $listOrder == 'a.ordering';
$input=jFactory::getApplication()->input;
if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_tjfields&task=groups.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'groupList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
$sortFields = $this->getSortFields();
?>
<script type="text/javascript">
	Joomla.submitbutton = function (task) {
		if (task == 'groups.delete')
		{
			if(confirm("<?php echo JText::_('COM_TJFIELDS_GROUPS_DELETE_CONFIRMATION'); ?>"))
			{
				Joomla.submitform(task);
			}
			else
			{
				return false;
			}
		}
		else
		{
			Joomla.submitform(task);
		}
	}

	Joomla.orderTable = function() {
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $listOrder; ?>') {
			dirn = 'asc';
		} else {
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_tjfields&view=groups&client='.$input->get('client','','STRING')); ?>" method="post" name="adminForm" id="adminForm">
	<div class="techjoomla-bootstrap">
		<?php if (!empty($this->sidebar)): ?>
			<div id="j-sidebar-container" class="span2"><?php echo $this->sidebar; ?></div>
				<div id="j-main-container" class="span10">
			<?php else : ?>
				<div id="j-main-container">
		<?php endif; ?>
			<div class="tjBs3">
				<div class="btn-group pull-left">
					<?php echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
				</div>
				<?php if(JVERSION >= '3.0'):?>
				<div class="btn-group pull-right hidden-phone">
					<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
					<?php echo $this->pagination->getLimitBox(); ?>
				</div>

				<div class="btn-group pull-right hidden-phone">
					<label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC');?></label>
					<select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
						<option value=""><?php echo JText::_('JFIELD_ORDERING_DESC');?></option>
						<option value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING');?></option>
						<option value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING');?></option>
					</select>
				</div>

				<div class="btn-group pull-right">
					<label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY');?></label>
						<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
							<option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
						<?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder);?>
						</select>
				</div>
					<?php endif;?>
			</div>
			<div class="clearfix"> </div>
			<?php
			if (empty($this->items))
			{
			?>
				<div class="alert alert-no-items">
					<?php echo JText::_('COM_TJFIELD_NO_GROUP_FOUND');?>
				</div>
				<?php
			}
			else
			{?>
			<table class="table table-striped" id="groupList">
				<thead>
					<tr>
					<?php if (isset($this->items[0]->ordering)): ?>
						<th width="1%" class="nowrap center hidden-phone">
							<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
						</th>
					<?php endif; ?>
						<th width="1%" class="hidden-phone">
							<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
						</th>
					<?php if (isset($this->items[0]->state)): ?>
						<th width="1%" class="nowrap center">
							<?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
						</th>
					<?php endif; ?>

					<th class='left'>
					<?php echo JHtml::_('grid.sort',  'COM_TJFIELDS_GROUPS_CREATED_BY', 'a.created_by_name', $listDirn, $listOrder); ?>
					</th>
					<th class='left'>
					<?php echo JHtml::_('grid.sort',  'COM_TJFIELDS_GROUPS_NAME', 'a.name', $listDirn, $listOrder); ?>
					</th>
					<th class='left'>
					<?php echo JHtml::_('grid.sort',  'COM_TJFIELDS_GROUPS_CLIENT', 'a.client', $listDirn, $listOrder); ?>
					</th>
					<?php if (isset($this->items[0]->id)): ?>
						<th width="1%" class="nowrap center hidden-phone">
							<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
						</th>
					<?php endif; ?>
					</tr>
				</thead>
				<tfoot>
					<?php
					if(isset($this->items[0])){
						$colspan = count(get_object_vars($this->items[0]));
					}
					else{
						$colspan = 10;
					}
				?>
				<tr>
					<td colspan="<?php echo $colspan ?>">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
				</tfoot>
				<tbody>
				<?php foreach ($this->items as $i => $item) :
					$ordering   = ($listOrder == 'a.ordering');
					$canCreate	= $user->authorise('core.create',		'com_tjfields');
					$canEdit	= $user->authorise('core.edit',			'com_tjfields');
					$canCheckin	= $user->authorise('core.manage',		'com_tjfields');
					$canChange	= $user->authorise('core.edit.state',	'com_tjfields');
					?>
					<tr class="row<?php echo $i % 2; ?>">

					<?php if (isset($this->items[0]->ordering)): ?>
						<td class="order nowrap center hidden-phone">
						<?php if ($canChange) :
							$disableClassName = '';
							$disabledLabel	  = '';
							if (!$saveOrder) :
								$disabledLabel    = JText::_('JORDERINGDISABLED');
								$disableClassName = 'inactive tip-top';
							endif; ?>
							<span class="sortable-handler hasTooltip <?php echo $disableClassName?>" title="<?php echo $disabledLabel?>">
								<i class="icon-menu"></i>
							</span>
							<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering;?>" class="width-20 text-area-order " />
						<?php else : ?>
							<span class="sortable-handler inactive" >
								<i class="icon-menu"></i>
							</span>
						<?php endif; ?>
						</td>
					<?php endif; ?>
						<td class="center hidden-phone">
							<?php echo JHtml::_('grid.id', $i, $item->id); ?>
						</td>
					<?php if (isset($this->items[0]->state)): ?>
						<td class="center">
							<?php echo JHtml::_('jgrid.published', $item->state, $i, 'groups.', $canChange, 'cb'); ?>
						</td>
					<?php endif; ?>

					<td>

						<?php echo $item->created_by_name; ?>
					</td>
					<td>
					<?php if (isset($item->checked_out) && $item->checked_out) : ?>
						<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'groups.', $canCheckin); ?>
					<?php endif; ?>
					<?php if ($canEdit) : ?>
						<a href="<?php echo JRoute::_('index.php?option=com_tjfields&task=group.edit&client='.$item->client.'&id='.(int) $item->id); ?>">
						<?php echo $this->escape($item->name); ?></a>
					<?php else : ?>
						<?php echo $this->escape($item->name); ?>
					<?php endif; ?>
					</td>
					<td>

						<?php echo $item->client; ?>
					</td>

					<?php if (isset($this->items[0]->id)): ?>
						<td class="center hidden-phone">
							<?php echo (int) $item->id; ?>
						</td>
					<?php endif; ?>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<?php
			}?>
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>
</div>
