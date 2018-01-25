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

		
$start = new JDate($this->item->start);
$end = new JDate($this->item->end);

$oneDay = CalSiteHelper::oneDay($start, $end);

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
			<h3><span class="glyphicon glyphicon-info-sign"></span> Details</h3>
			<div class="cal-event-dates">
				<?php if($oneDay): ?>
				<div class="cal-event-date">
					<?php echo CalSiteHelper::weekday($start).', '.JHTML::date($start, 'd.m.Y'); ?>
				</div>
				<?php endif; ?>
				<?php if(!$oneDay): ?>
				<div class="cal-event-start">	
					<span class="cal-event-start-date">Beginn: <?php echo CalSiteHelper::weekday($start).', '.JHTML::date($start, 'd.m.Y'); ?></span>
				<?php else: ?>
				<div class="cal-event-times">
				<?php endif; ?>
					<time class="cal-event-start-time"><?php echo JHTML::date($start, "H:i") ?></time> – 
					<span class="hidden" itemprop="startDate"><?php echo $start->toISO8601(); ?></span>
				<?php if(!$oneDay): ?>
				</div>
				<div class="cal-event-end">	
					<span class="cal-event-end-date">Ende: <?php echo CalSiteHelper::weekday($end).', '.JHTML::date($end, 'd.m.Y'); ?></span>
				<?php endif; ?>
					<time class="cal-event-end-time"><?php echo JHTML::date($end, "H:i") ?></time>
					<span class="hidden" itemprop="endDate"><?php echo $end->toISO8601(); ?></span>
				</div>
			</div>
			<div class="cal-event-desc" itemprop="description">
				<?php echo $this->item->introtext.$this->item->fulltext; ?>
			</div>
			<?php if($this->item->link): ?>
			<a href="<?php echo $this->item->link; ?>" class="cal-event-link btn btn-default" itemprop="url">
					Weitere Informationen
			</a>
			<a href="<?php echo JRoute::_('index.php?option=com_cal&view=event&format=ics&id='.$this->item->id);?>" class="cal-event-ical btn btn-default">iCal / Outlook</a>
			<?php endif; ?>
		</div>
		<div class="cal-event-right col-md-6" itemprop="location" itemscope itemtype="http://schema.org/Place">
			<h3><span class="glyphicon glyphicon-map-marker"></span> <span itemprop="name"><?php echo $this->item->loc_name; ?></span></h3>
			<div class="cal-event-loc" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
				<?php if($this->item->addrStreet): ?><div class="cal-event-loc-street" itemprop="streetAddress"><?php echo $this->item->addrStreet; ?></div><?php endif; ?>
				<?php if($this->item->addrZip): ?><div class="cal-event-loc-zip" itemprop="postalCode"><?php echo $this->item->addrZip; ?></div><?php endif; ?>
				<?php if($this->item->addrCity): ?><div class="cal-event-loc-city" itemprop="addressLocality"><?php echo $this->item->addrCity; ?></div><?php endif; ?>
				<?php if($this->item->addrCountry): ?><div class="cal-event-loc-country" itemprop="addressCountry"><?php echo $this->item->addrCountry; ?></div><?php endif; ?>
			</div>
			<?php if($this->item->loc_link): ?>
			<div class="cal-event-loc-link">
				<a itemprop="url" class="btn btn-default" href='<?php echo $this->item->loc_link; ?>' target="_new">Mehr zu <?php echo $this->item->loc_name; ?></a>
			</div>
			<?php endif; ?>
			<div class="cal-event-map">
				<?php if($this->item->geoX !== null): ?>
				<div class="hidden" itemprop="geo" itemscope itemtype="http://schema.org/GeoCoordinates">
					<span itemprop="latitude"><?php echo $this->item->geoX; ?></span>
					<span itemprop="longitude"><?php echo $this->item->geoY; ?></span>
				</div>
				<?php if($this->mapsUse): ?>
				<div id="cal-event-map-map"></div>
				<script>
					function initMap() {
					  var loc = {lat: <?php echo $this->item->geoX; ?> , lng: <?php echo $this->item->geoY; ?>};
					  var map = new google.maps.Map(document.getElementById('cal-event-map-map'), {
						zoom:14,
						center: loc,
						mapTypeId: 'roadmap',
						disableDefaultUI: true,
						zoomControl: true,
						rotateControl: true,
						fullscreencontrol: true
					  });
					  var marker = new google.maps.Marker({
						position: loc,
						map: map
					  });
					}
				</script>
				<script async defer
					src="https://maps.googleapis.com/maps/api/js?key=<?php echo $this->mapsKey; ?>&callback=initMap">
				</script>
				<?php endif; endif; ?>
			</div>
		</div>
	</div>
	<div class="cal-event-related">
		<h3>Ähnliche Veranstaltungen</h3>
		<table class="table">
			<tbody>
				<?php foreach($this->related as $event): 
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
</div>