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

$timezone = CalHelper::getTimeZone();
		
$start = new JDate($this->item->start);
$start->setTimezone($timezone);
$end = new JDate($this->item->end);
$end->setTimezone($timezone);

$oneDay = CalHelper::oneDay($start, $end);

?>
<div class="cal-event" itemscope itemtype="https://schema.org/Event">
	<?php if ($this->params->get('show_page_heading')) : ?>
	<div>
		<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
	</div>
	<?php endif; ?>
	
	<div class="page-header">
		<h2 itemprop="name"><?php echo $this->escape($this->item->name); ?></h2>
	</div>
	<div class="cal-event-body row">
		<div class="cal-event-left col-md-6">
			<h3>Details</h3>
			<div class="cal-event-dates">
				<?php if($oneDay): ?>
				<div class="cal-event-date">
					<?php echo CalHelper::weekday($start).', '.$start->format('d.m.Y'); ?>
				</div>
				<?php endif; ?>
				<div class="cal-event-start">
					<?php if(!$oneDay): ?>
					<span class="cal-event-start-date"><?php echo CalHelper::weekday($start).', '.$start->format('d.m.Y'); ?></span>
					<?php endif; ?>
					<time class="cal-event-start-time"><?php echo $start->format("H:i") ?></time>
					<span class="hidden" itemprop="startDate"><?php echo $start->toISO8601(); ?></span>
				</div>
				<div class="cal-event-end">
					<?php if(!$oneDay): ?>
					<span class="cal-event-end-date"><?php echo CalHelper::weekday($end).', '.$end->format('d.m.Y'); ?></span>
					<?php endif; ?>
					<time class="cal-event-end-time"><?php echo $end->format("H:i") ?></time>
					<span class="hidden" itemprop="endDate"><?php echo $end->toISO8601(); ?></span>
				</div>
			</div>
			<div class="cal-event-desc" itemprop="description">
				<?php echo $this->item->introtext.$this->item->fulltext; ?>
			</div>
		</div>
		<div class="cal-event-right col-md-6" itemprop="location" itemscope itemtype="http://schema.org/Place">
			<h3 itemprop="name"><?php echo $this->item->loc_name; ?></h3>
			<div class="cal-event-loc" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
				<?php if($this->item->addrStreet): ?><div class="cal-event-loc-street" itemprop="streetAddress"><?php echo $this->item->addrStreet; ?></div><?php endif; ?>
				<?php if($this->item->addrZip): ?><div class="cal-event-loc-zip" itemprop="postalCode"><?php echo $this->item->addrZip; ?></div><?php endif; ?>
				<?php if($this->item->addrCity): ?><div class="cal-event-loc-city" itemprop="addressLocality"><?php echo $this->item->addrCity; ?></div><?php endif; ?>
				<?php if($this->item->addrCountry): ?><div class="cal-event-loc-country" itemprop="addressCountry"><?php echo $this->item->addrCountry; ?></div><?php endif; ?>
			</div>
			<?php if($this->item->loc_link): ?>
			<div class="cal-event-loc-link">
				<a itemprop="url" class="btn" href='<?php echo $this->item->loc_link; ?>' target="_new">Mehr zu <?php echo $this->item->loc_name; ?></a>
			</div>
			<?php endif; ?>
			<div class="cal-event-map">
				
				<?php if($this->item->geoLoc): ?>
				<div class="hidden" itemprop="geo" itemscope itemtype="http://schema.org/GeoCoordinates">
					<span itemprop="latitude"></span>
					<span itemprop="longitude"></span>
				</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>