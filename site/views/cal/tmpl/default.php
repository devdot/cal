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

$paginationDisable = !(bool)($this->state->start);
?>
<div class="cal-cal">
	<?php if ($this->params->get('show_page_heading')) : ?>
	<div class="page-header">
		<h1> <?php echo $this->escape($this->params->get('page_heading')); ?> </h1>
	</div>
	<?php endif; ?>
	
	<div class="page-header">
		<h2><?php echo $this->escape($this->params->get('page_title')); ?></h2>
	</div>
	<table class="table cal-table">
		<thead class="cal-table-hide-sm">
			<tr>
				<th>Sonntag</th>
				<th>Montag</th>
				<th>Dienstag</th>
				<th>Mittwoch</th>
				<th>Donnerstag</th>
				<th>Freitag</th>
				<th>Samstag</th>
			</tr>
		</thead>
		<tbody>
			<?php 
			$interval = new DateInterval('P1D');
			$current = clone $this->start;
			$nextTS = $current->getTimestamp() + 86400; //next day
			$d = CalSiteHelper::month($current);
			$nextMonth = true; //flag that is true when d contains the name of the next month
			$i = 0;
			$today = new JDate();
			while($current < $this->end): ?>
			<tr>
			<?php
				for($j = 0; $j < 7; $j++):
					//look up whether we should add hide-sm to this td
					$showSm = $i < count($this->items);
					if($showSm) {
						$start = new JDate($this->items[$i]->start);
						$showSm = $start->day == $current->day?true:false;
					}
					$showToday = $today->day == $current->day && $today->month == $current->month && $today->year == $current->year;
				?>
				<td class="<?php echo $showSm?'':'cal-table-hide-sm'; echo $showToday?' cal-table-today':''; ?>">
					<div class="cal-table-date cal-table-hide-lg"><?php echo CalSiteHelper::$weekdays[$j].', '.$current->day.'.'.$current->month; ?></div>
					<div class="cal-table-date<?php echo ($nextMonth)?'-month':'-day'; ?> cal-table-hide-sm"><?php echo $d; ?></div>
					<?php
					while($i < count($this->items)):
							$start = new JDate($this->items[$i]->start);
						if($start->getTimestamp() >= $nextTS)
							break;
						//$start->setTimezone($timezone); timezone is somehow really broken
					?>
					<a class="cal-table-event" href="<?php echo JRoute::_('index.php?option=com_cal&view=event&id='.$this->items[$i]->id) ?>">
						<span class="cal-table-event-time"><?php echo JHTML::date($start, "H:i"); ?></span>
						<span class="cal-table-event-name"><?php echo $this->items[$i]->name; ?></span>
					</a>
					<?php
						$i++;
					endwhile;
					?>
				</td>
				<?php
					if($nextMonth) {
						$nextMonth = false;
						$d = $current->day;
					}
					$d++;
					$current->add($interval);
					$nextTS += 86400;
					if($d > 28 && $current->day != $d) {
						$d = CalSiteHelper::month($current);
						$nextMonth = true;
					}
				endfor;
			?>
			</tr>
			<?php
			endwhile;
			?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="7">
					<div class="pagination-wrapper">
						<ul class="pagination">
							<li class="<?php echo $paginationDisable?'disabled':''; ?> hidden-xs">
								<a href="<?php echo $paginationDisable?'':JRoute::_('index.php?option=com_cal&view=cal&start=0'); ?>"><span class="glyphicon glyphicon-step-backward"></span></a>
							</li>
							<li class="<?php echo $paginationDisable?'disabled':''; ?>">
								<a href="<?php echo $paginationDisable?'':JRoute::_('index.php?option=com_cal&view=cal&start='.($this->state->start - 4)); ?>"><span class="glyphicon glyphicon-chevron-left"></span></a>
							</li>
							<li>
								<a href="<?php echo JRoute::_('index.php?option=com_cal&view=cal&start='.($this->state->start + 4)); ?>" class="pagenav"><span class="glyphicon glyphicon-chevron-right"></span></a>
							</li>
						</ul>
					</div>
				</td>
			</tr>
		</tfoot>
	</table>
	<a href="<?php echo JRoute::_('index.php?view=events'); ?>">Listenansicht, weitere Termine</a>
</div>