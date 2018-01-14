<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_cal
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
 
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

?>
<div class="cal-events">
	<?php if ($this->params->get('show_page_heading')) : ?>
	<div class="page-header">
		<h1> <?php echo $this->escape($this->params->get('page_heading')); ?> </h1>
	</div>
	<?php endif; ?>
	
	<div class="page-header">
		<h2><?php echo $this->escape($this->params->get('page_title')); ?></h2>
	</div>
	
	<form id="cal-events-form" action="<?php echo JRoute::_('?option=com_cal&view=events'); ?>" method="post" name="cal-events-form">
		<div class="row cal-events-filters">
			<div class="col-sm-6 col-xs-6">
				<div class="input-group">
					<?php echo $this->filterForm->getField('search', 'filter')->input; ?>
					<div class="input-group-btn">
						<button type="submit" class="btn btn-default" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>">
							<span class="glyphicon glyphicon-search"></span>
						</button>
					</div>
				</div>
			</div>
			<div class="col-sm-3 col-xs-6 form-group">
				<?php echo $this->filterForm->getField('catid', 'filter')->input; ?>
			</div>
			<div class="col-sm-3 form-group hidden-xs form-inline">
				<div class="form-group">
					<button type="button" class="btn" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>">
						<?php echo JText::_('JSEARCH_FILTER_CLEAR');?>
					</button>
					<?php echo $this->filterForm->getField('limit', 'list')->input; ?>
				</div>
			</div>
		</div>
		
		<?php if (empty($this->items)) : ?>
		<div class="alert alert-warning">
			<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
		</div>
		<?php endif; ?>
		<table class="table table-striped">
			<thead>
				<tr>
					<th>Datum</th>
					<th>Veranstaltung</th>
					<th class="hidden-xs">Kategorie</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($this->items as $event): 
					$start = new JDate($event->start);
					$end =   new JDate($event->end);
					//check if this event is one day long or across multiple days
					$oneDay = CalSiteHelper::oneDay($start, $end);
				?>
				<tr class='clickable-row' data-href="<?php echo JRoute::_('index.php?option=com_cal&view=event&id='.$event->id); ?>">
					<td>
						<?php echo CalSiteHelper::weekday($start).', '.JHTML::date($start, "d.m.Y");?>
						<?php if($oneDay): 
								echo '<br>'.JHTML::date($start, 'H:i').' &ndash; '.JHTML::date($end, 'H:i'); 
							else:
								echo JHTML::date($start, 'H:i').' &ndash;<br>'.CalSiteHelper::weekday($end).', '.JHTML::date($end, "d.m.Y H:i");
						endif; ?>
					</td>
					<td><?php echo $event->name; ?></td>
					<td class="hidden-xs"><?php echo $event->cat_name; ?></td>
				</tr>	
				<?php endforeach; ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="3"><?php echo $this->pagination->getListFooter(); ?></td>
				</tr>
			</tfoot>
		</table>
	</form>
</div>