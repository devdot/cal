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
JFactory::getDocument()->addStyleSheet(JURI::base().'components/com_cal/css/cal.css');

?>
<form id="adminForm" action="?option=com_cal&view=event" method="post" name="adminForm">
    <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10 j-toggle-main">
        <div class="span6">
            <h2><?php echo JText::_('COM_CAL_QUICKACCESS'); ?></h2>
			<div class="cal-cpanel">
				<a class="btn" onclick="Joomla.submitbutton('event.add')">
					<span class="cal-cpanel-icon"><span class="icon icon-plus"></span></span>
					<span class="cal-cpanel-title"><?php echo JText::_("COM_CAL_CPANEL_NEW_EVENT"); ?></span>
				</a>
				<a class="btn" onclick="Joomla.submitbutton('events.recurring')">
					<span class="cal-cpanel-icon"><span class="icon icon-loop"></span></span>
					<span class="cal-cpanel-title"><?php echo JText::_("COM_CAL_CPANEL_RECURRING"); ?></span>
				</a>
				<a class="btn" onclick="Joomla.submitbutton('events.archive')">
					<span class="cal-cpanel-icon"><span class="icon icon-archive"></span></span>
					<span class="cal-cpanel-title"><?php echo JText::_("COM_CAL_CPANEL_ARCHIVE"); ?></span>
				</a>
			</div>
        </div>
        <div class="span6">
            <h2><?php echo JText::_('COM_CAL_NEW_EVENTS'); ?></h2>
			<table class="table table-striped" id="cal-locations">
				<thead>
					<tr>
						<th class="nowrap">
							<?php echo JText::_('COM_CAL_NAME'); ?>
						</th>
						<th class="nowrap">
							<?php echo JText::_('COM_CAL_START'); ?>
						</th>
						<th class="nowrap">
							<?php echo JText::_('COM_CAL_LOCATION'); ?>
						</th>
						<th class="nowrap">
							<?php echo JText::_('COM_CAL_AUTHOR'); ?>
						</th>
					</tr>
				</thead>
				<tbody>
				<?php
				$n = count($this->items);
				foreach ($this->items as $i => $item) :
					?>
					<tr class="row<?php echo $i % 2; ?>">
						<td class="has-context">
							<div class="pull-left break-word">
								
								<a href="<?php echo JRoute::_('index.php?option=com_cal&task=event.edit&id=' . (int) $item->id); ?>"><?php echo $item->name; ?></a>
								<?php if($item->recurring_id): ?>
									<a href="<?php echo JRoute::_('index.php?option=com_cal&task=event.edit&id=' . (int) $item->recurring_id); ?>"><span class="icon-loop"></span></a>
								<?php endif; ?>
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
							echo JHTML::date($date, 'Y-m-d H:i'); 
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
							<?php if($item->user_name): ?>
							<a href="?option=com_users&task=user.edit&id=<?php echo $item->created_by; ?>"><?php echo $item->user_name; ?></a>
							<?php endif; ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
        </div>
    </div>
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <?php echo JHtml::_('form.token'); ?>
</form>
