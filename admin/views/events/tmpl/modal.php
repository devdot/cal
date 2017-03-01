<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$app = JFactory::getApplication();

if ($app->isSite())
{
	JSession::checkToken('get') or die(JText::_('JINVALID_TOKEN'));
}

JLoader::register('ContentHelperRoute', JPATH_ROOT . '/components/com_content/helpers/route.php');

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('behavior.core');
JHtml::_('bootstrap.tooltip', '.hasTooltip', array('placement' => 'bottom'));
JHtml::_('formbehavior.chosen', 'select');

// Special case for the search field tooltip.
$searchFilterDesc = $this->filterForm->getFieldAttribute('search', 'description', null, 'filter');
JHtml::_('bootstrap.tooltip', '#filter_search', array('title' => JText::_($searchFilterDesc), 'placement' => 'bottom'));

$function  = $app->input->getCmd('function', 'jSelectArticle');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));


$recParent = ((int) $this->state->get('filter.recurring')) == 1; //if the filter is 1 (show recurring parents), we have to handle some things differently
?>
<div class="container-popup">

	<form action="<?php echo JRoute::_('index.php?option=com_cal&view=events&layout=modal&tmpl=component&function=' . $function . '&' . JSession::getFormToken() . '=1'); ?>" method="post" name="adminForm" id="adminForm" class="form-inline">

		<?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>

		<div class="clearfix"></div>

		<?php if (empty($this->items)) : ?>
			<div class="alert alert-no-items">
				<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
			</div>
		<?php else : ?>
			<table class="table table-striped table-condensed">
				<thead>
					<tr>
						<th class="nowrap">
							<?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'state', $listDirn, $listOrder); ?>
						</th>
                        <th class="nowrap">
							<?php echo JHtml::_('searchtools.sort', 'COM_CAL_NAME', 'name', $listDirn, $listOrder); ?>
						</th>
						<th class="nowrap">
							<?php echo JHtml::_('searchtools.sort', 'COM_CAL_START', 'start', $listDirn, $listOrder); ?>
						</th>
						<th class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'COM_CAL_END', 'end', $listDirn, $listOrder); ?>
						</th>
						<th class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'COM_CAL_LOCATION', 'location_name', $listDirn, $listOrder); ?>
						</th>
						<th class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'COM_CAL_AUTHOR', 'user_name', $listDirn, $listOrder); ?>
						</th>
						<th class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'COM_CAL_ACCESS', 'access_name', $listDirn, $listOrder); ?>
						</th>
						<th width="1%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder); ?>
						</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="6">
							<?php echo $this->pagination->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>
				<tbody>
				<?php
				$iconStates = array(
					-2 => 'icon-trash',
					0 => 'icon-unpublish',
					1 => 'icon-publish',
					2 => 'icon-archive',
				);
				?>
				<?php foreach ($this->items as $i => $item) : ?>
					<tr class="row<?php echo $i % 2; ?>">
						<td class="center">
							<span class="<?php echo $iconStates[$this->escape($item->state)]; ?>"></span>
						</td>
						<td class="has-context">
							<div class="pull-left break-word">
								<?php if ($item->checked_out) : ?>
									<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'events.', true); ?>
								<?php endif; ?>
								<a href="javascript:void(0);" onclick="if (window.parent) window.parent.<?php echo $this->escape($function); ?>('<?php echo $item->id; ?>', '<?php echo $this->escape(addslashes($item->name)); ?>', '<?php echo $this->escape($item->catid); ?>', null, '<?php /*router*/ ?>', null);">
									<?php echo $item->name; ?>
								</a>
								<span class="small break-word">
									<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias)); ?>
								</span>
								<div class="small">
									<?php echo JText::_('JCATEGORY') . ": "; ?>
									<?php echo $this->escape($item->cat_name); ?>
								</div>
							</div>
						</td>
						<td>
							<?php 
							echo JHTML::date($item->start, 'Y-m-d H:i');
							?>
						</td>
						<td>
							<?php 
							echo JHTML::date($item->end, 'Y-m-d H:i');
							?>
						</td>
						<td>
							<?php if($item->location_id): ?>
								<?php echo $this->escape($item->location_name); ?>
							<?php endif; ?>
						</td>
						<td class="hidden-phone">
							<?php if($item->user_name): ?>
								<?php echo $item->user_name; ?>
							<?php endif; ?>
						</td>
						<td class="hidden-phone">
							<?php echo $item->access_name; ?>
						</td>
						<td class="hidden-phone">
							<?php echo $item->id; ?>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHtml::_('form.token'); ?>

	</form>
</div>
