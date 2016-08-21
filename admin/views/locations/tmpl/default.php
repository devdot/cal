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

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>
<form id="adminForm" action="?option=com_cal&view=locations" method="post" name="adminForm">
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
						<th width="1%" style="min-width:55px" class="nowrap center">
							<?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'published', $listDirn, $listOrder); ?>
						</th>
						<th class="nowrap">
							<?php echo JHtml::_('searchtools.sort', 'Name', 'name', $listDirn, $listOrder); ?>
						</th>
                        <th class="nowrap">
							<?php echo JHtml::_('searchtools.sort', 'Street', 'addrStreet', $listDirn, $listOrder); ?>
						</th>
                        <th class="nowrap">
							<?php echo JHtml::_('searchtools.sort', 'ZIP', 'addrZip', $listDirn, $listOrder); ?>
						</th>
						<th width="1%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'ID', $listDirn, $listOrder); ?>
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
							<?php echo JHtml::_('grid.id', $i, $item->ID); ?>
						</td>
						<td class="center">
							<div class="btn-group">
								<?php echo JHtml::_('jgrid.published', $item->published, $i, 'location.', 'cb'); ?>
							</div>
						</td>
                        <td class="hidden-phone">
                            <a href="<?php echo JRoute::_('index.php?option=com_cal&task=location.edit&id=' . (int) $item->ID); ?>"><?php echo $item->name; ?></a>
						</td>
						<td class="hidden-phone">
							<?php echo $item->addrStreet; ?>
						</td>
                        <td class="hidden-phone">
							<?php echo $item->addrZip; ?>
						</td>
						<td class="hidden-phone">
							<?php echo $item->ID; ?>
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


