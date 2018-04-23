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
$headline = $this->params->get('show_page_heading')?$this->params->get('page_heading'):$this->params->get('page_title');

?>
<div class="cal-category">
	<?php if ($this->params->get('show_page_heading')) : ?>
	<div>
		<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
	</div>
	<?php endif; ?>
	
	<div class="page-header">
		<h2><?php echo $this->escape($this->item->title); ?></h2>
	</div>
	<div class="cal-category-body">
		<?php echo $this->item->description; ?>
	</div>
	<?php if(count($this->events)): ?>
	<div class="cal-category-upcoming">
		<h3>Nächte Veranstaltungen</h3>
		<table class="table">
			<tbody>
				<?php foreach($this->events as $event): 
					$start = new JDate($event->start);
					$end =   new JDate($event->end);
					//check if this event is one day long or across multiple days
					$oneDay = CalSiteHelper::oneDay($start, $end);
				?>
				<tr class='clickable-row' data-href="<?php echo JRoute::_('index.php?option=com_cal&view=event&id='.$event->id); ?>">
					<td><?php echo JHTML::date($start, "d.m.");?></td>
					<td><?php echo JHTML::date($start, 'H:i').' &ndash; ';
						echo $oneDay?'':JHTML::date($end, 'd.m. ');
						echo JHTML::date($end, 'H:i') ?></td>
					<td><?php echo $event->name; ?></td>
				</tr>	
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<?php endif; ?>
</div>