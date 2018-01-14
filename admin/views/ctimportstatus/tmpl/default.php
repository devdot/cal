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

?>
<form id="adminForm" action="?option=com_cal&view=ct_importStatus" method="post" name="adminForm">
    <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10 j-toggle-main">
		<div class="clearfix"></div>
		<?php if (empty($this->items)) : ?>
		<div class="alert alert-no-items">
			<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
		</div>
        <?php else: ?>
            <table class="table table-striped" id="cal-ct-importStatus">
				<thead>
					<tr>
						<th width="1%" class="nowrap center">
							<?php echo JHtml::_('grid.checkall'); ?>
						</th>
                        <th width="1%" class="nowrap">
							
						</th>
                        <th class="nowrap">
							<?php echo JText::_('COM_CAL_NAME'); ?>
						</th>
						<th class="nowrap">
							<?php echo JText::_('COM_CAL_START'); ?>
						</th>
						<th class="nowrap">
							<?php echo JText::_('COM_CAL_END'); ?>
						</th>
						<th width="1%" class="nowrap">
							<?php echo JText::_('JCATEGORY'); ?>
						</th>
						<th class="nowrap">
							<?php echo JText::_('COM_CAL_CT_MODIFIED'); ?>
						</th>
						<th width="1%" class="nowrap hidden-phone">
							<?php echo JText::_('JGRID_HEADING_ID'); ?>
						</th>
						<th width="1%" class="nowrap hidden-phone">
							<?php echo JText::_('COM_CAL_CT_SUBID'); ?>
						</th>
						<th width="1%" class="nowrap hidden-phone">
							<?php echo JText::_('COM_CAL_CT_EVENTID'); ?>
						</th>
					</tr>
				</thead>
                <tbody>
				<?php
				$n = count($this->items);
				foreach ($this->items as $i => $item) :
					switch($item->ctState) {
						case '1':
							$class = ' success';
							break;
						case '2':
							$class = ' warning';
							break;
						default:
							$class = '';
					}
					?>
					<tr class="row<?php echo $i % 2; echo $class?>" sortable-group-id="<?php echo $item->catid; ?>">
						<td class="center">
							<?php if($item->ctState !== 1) echo JHtml::_('grid.id', $i, $item->id.'_'.$item->subid); ?>
						</td>
						<td class="hidden-phone">
							<div class="btn-group">
								
							</div>
						</td>
                        <td>
							<?php if($item->ctState): ?>
                            <a href="<?php echo JRoute::_('index.php?option=com_cal&task=event.edit&id=' . (int) $item->event_id); ?>"><?php echo $item->name; ?></a>
							<?php else:
								echo $item->name; 
							endif; ?>
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
							<?php 
							echo $item->category_id;
							?>
						</td>
						<td>
							<?php 
							echo JHTML::date($item->modified, 'Y-m-d H:i');
							?>
						</td>
						<td class="hidden-phone">
							<?php echo $item->id; ?>
						</td>
						<td class="hidden-phone">
							<?php echo $item->subid; ?>
						</td>
						<td class="hidden-phone">
							<?php echo $item->event_id; ?>
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


