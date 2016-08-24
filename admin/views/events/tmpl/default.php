<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_cal
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));

$timezone = CalHelper::getTimeZone();
?>
<form id="adminForm" action="?option=com_cal&view=events" method="post" name="adminForm">
    <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10 j-toggle-main">
        <?php  echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
		<div class="clearfix"></div>
		<?php if (empty($this->items)) : ?>
		<div class="alert alert-no-items">
			<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
		</div>
        <?php else: ?>
            <table class="table table-striped" id="cal-locations">
				<thead>
					<tr>
						<th width="1%" class="nowrap center">
							<?php echo JHtml::_('grid.checkall'); ?>
						</th>
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
							<?php echo JHtml::_('searchtools.sort', 'COM_CAL_ACCESS', 'access_name', $listDirn, $listOrder); ?>
						</th>
						<th width="1%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder); ?>
						</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="10">
							<?php echo $this->pagination->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>
                <tbody>
				<?php
				$n = count($this->items);
				foreach ($this->items as $i => $item) :
					?>
					<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->catid; ?>">
						<td class="center">
							<?php echo JHtml::_('grid.id', $i, $item->id); ?>
						</td>
						<td class="hidden-phone">
							<div class="btn-group">
								<?php echo JHtml::_('jgrid.published', $item->state, $i, 'events.'); ?>
								<?php 
									JHtml::_('actionsdropdown.' . ((int) $item->state === -2 ? 'un' : '') . 'trash', 'cb' . $i, 'events');
									echo JHtml::_('actionsdropdown.render', $this->escape($item->name));
								?>
							</div>
						</td>
						<td class="has-context">
							<div class="pull-left break-word">
								<?php if ($item->checked_out) : ?>
									<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'events.', true); ?>
								<?php endif; ?>
								<a href="<?php echo JRoute::_('index.php?option=com_cal&task=event.edit&id=' . (int) $item->id); ?>"><?php echo $item->name; ?></a>
								<?php if($item->recurring_id): ?>
								<a href="<?php echo JRoute::_('index.php?option=com_cal&task=event.edit&id=' . (int) $item->recurring_id); ?>"><span class="icon-loop"></span></a>
								<?php endif; ?>
								<span class="small break-word">
									<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias)); ?>
								</span>
								<div class="small">
									<?php echo JText::_('JCATEGORY') . ": "; ?>
									<a href="<?php echo JRoute::_('index.php?option=com_categories&extension=com_cal&task=category.edit&id=' . (int) $item->catid); ?>">
										<?php echo $this->escape($item->cat_name); ?>
									</a>
								</div>
							</div>
						</td>
						<td>
							<?php 
							$date = new JDate($item->start);
							$date->setTimezone($timezone);
							echo $date->format('Y-m-d H:i'); 
							?>
						</td>
						<td>
							<?php 
							$date = new JDate($item->end);
							$date->setTimezone($timezone);
							echo $date->format('Y-m-d H:i'); 
							?>
						</td>
						<td>
							<?php if($item->location_id): ?>
							<a href="<?php echo JRoute::_('index.php?option=com_cal&task=location.edit&ID=' . (int) $item->location_id); ?>">
								<?php echo $this->escape($item->location_name); ?>
							</a>
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
    </div>
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <?php echo JHtml::_('form.token'); ?>
</form>
