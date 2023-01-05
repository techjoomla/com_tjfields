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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');

// Load lang file for countries
$lang = Factory::getLanguage();
$lang->load('tjgeo.countries', JPATH_SITE, null, false, true);
$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');
$user = Factory::getUser();
?>
<?php
if (! empty($this->extra_sidebar))
{
	$this->sidebar .= $this->extra_sidebar;
}
?>

<div class="<?php echo TJFIELDS_WRAPPER_CLASS;?> tj-conditions">
	<?php if (!empty($this->sidebar)): ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">

	<?php else : ?>
		<div id="j-main-container">
		<?php endif; ?>
	<form
		action="<?php echo Route::_('index.php?option=com_tjfields&view=conditions&client=' . $this->input->get('client', '', 'STRING')); ?>"
		method="post" name="adminForm" id="adminForm">

			<?php if (empty($this->items)) : ?>
				<div class="clearfix">&nbsp;</div>
				<div class="alert alert-no-items">
					<?php echo Text::_('COM_TJFIELDS_NO_MATCHING_RESULTS'); ?>
				</div>
			<?php
			else : ?>
				<table class="table table-striped" id="countryList">
					<thead>
						<tr>
							<th width="1%" class="hidden-phone">
								<input type="checkbox" name="checkall-toggle" value=""
								title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>"
								onclick="Joomla.checkAll(this)" />
							</th>
							<?php if (isset($this->items[0]->state)): ?>
								<th width="1%" class="nowrap center">
									<?php echo HTMLHelper::_('grid.sort', 'JSTATUS', 'state', $listDirn, $listOrder); ?>
								</th>
							<?php endif; ?>

							<th class='left'>
								<?php echo Text::_('Condition'); ?>
							</th>

							<th class="center hidden-phone">
								<?php echo Text::_('Actions'); ?>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ($this->items as $i => $item):
						$canChange = $user->authorise('core.edit.state', 'com_tjfields');
						?>
							<tr class="row<?php echo $i % 2; ?>">
								<?php if (JVERSION >= '3.0'): ?>
									<td class="center hidden-phone">
										<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
									</td>
									<?php if (isset($this->items[0]->state)): ?>
										<td class="center">
											<?php echo HTMLHelper::_('jgrid.published', $item->state, $i, 'conditions.', $canChange, 'cb'); ?>
										</td>
									<?php endif; ?>
									<td>
										<?php 
										if ($item->show == 1)
										{
											$showHide = "Show";
										}
										else
										{
											$showHide = "Hide";
										}
										
										if ($item->condition_match == 1)
										{
											$conditionMatch = "All";
										}
										else
										{
											$conditionMatch = "Any";
										} 
										?>
										<div>
										<?php echo $showHide . ' - ' . $this->model->getFieldName($item->field_to_show); ?>
										</div>
										<div>
											<ul>	
												<li><strong><?php echo $conditionMatch; ?></strong></li>
												<?php foreach (json_decode($item->condition) as $condition) { ?>
													<?php 
													$conditionObj = json_decode($condition);
													
													if ($conditionObj->operator == 1)
													{
														$operator = "Is";
													}
													else
													{
														$operator = "Is Not";
													}
													?> 
												<li><?php echo $this->model->getFieldName($conditionObj->field_on_show) . ' ';  ?>
												<strong><?php  echo $operator; ?></strong>
												<?php echo ' ' . $this->model->getOptionName($conditionObj->field_on_show, $conditionObj->option); ?></li>
												<?php } ?>
											</ul>
										</div>
									</td>
									<td>
										<a href="<?php echo Route::_('index.php?option=com_tjfields&task=condition.edit&id=' . (int) $item->id . '&client=' . $this->input->get('client', '', 'STRING')); ?>">
										<?php echo "Edit"; ?>
										</a>
										</br>
										<a href="<?php echo Route::_('index.php?option=com_tjfields&task=conditions.delete&id=' . (int) $item->id . '&client=' . $this->input->get('client', '', 'STRING')); ?>">
										<?php echo "Delete"; ?>
										</a>
									</td>
								<?php endif; ?>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>

			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
			<?php echo HTMLHelper::_('form.token'); ?>
		</div>
	</form>
</div>
