<?php 
// No direct access

defined('_JEXEC') or die;?>
<div class="mod-cal-small">
	<h2 class="mod-cal-small-headline">Nächste Veranstaltungen</h2>
	<div class="mod-cal-small-outer">
		<div class="mod-cal-small-prev" onclick="mod_cal_small_prev()">
			<span class="glyphicon glyphicon-chevron-left"></span>
		</div>
		<div class="mod-cal-small-inner">
			<?php if(count($events['prev']) == 5): //it's filled, use extra page for redirect ?>
			<table class="table table-hover">
				<?php
				for($i = 0; $i < 5; $i++): ?>
				<tr class="empty">
					<td></td>
					<td><?php echo $i==2?'weitere Termine LINK':'&nbsp;'; ?></td>
					<td></td>
				</tr>	
				<?php endfor; ?>
			</table>
			<?php endif; 
				if(count($events['prev'])): ?>
			<table class="table table-hover">
				<?php
				foreach($events['prev'] as $event): 
					$start = new JDate($event->start);
					$end =   new JDate($event->end);
					//check if this event is one day long or across multiple days
					$oneDay = ($start->day == $end->day && $start->month == $end->month);
				?>
				<tr class='clickable-row' data-href="<?php echo JRoute::_('index.php?option=com_cal&view=event&id='.$event->id); ?>">
					<td><?php echo JHTML::date($start, "d.m.");?></td>
					<td><?php echo $event->name; ?></td>
					<td><?php echo JHTML::date($start, 'H:i').' &ndash; ';
						echo $oneDay?'':JHTML::date($end, 'd.m. ');
						echo JHTML::date($end, 'H:i') ?></td>
				</tr>	
				<?php endforeach;
					for($i = count($events['prev']); $i < 5; $i++): //fill missing rows ?>
				<tr class="empty">
					<td></td>
					<td>&nbsp;</td>
					<td></td>
				</tr>	
				<?php endfor; ?>
			</table>
			<?php endif; ?>
			<table class="table table-hover mod-cal-small-active">
				<?php
					foreach($events['main'] as $event): 
						$start = new JDate($event->start);
						$end =   new JDate($event->end);
						//check if this event is one day long or across multiple days
						$oneDay = ($start->day == $end->day && $start->month == $end->month);
				?>
				<tr class='clickable-row' data-href="<?php echo JRoute::_('index.php?option=com_cal&view=event&id='.$event->id); ?>">
					<td><?php echo JHTML::date($start, "d.m.");?></td>
					<td><?php echo $event->name; ?></td>
					<td><?php echo JHTML::date($start, 'H:i').' &ndash; ';
						echo $oneDay?'':JHTML::date($end, 'd.m. ');
						echo JHTML::date($end, 'H:i') ?></td>
				</tr>	
				<?php endforeach; ?>
			</table>
			<?php if(count($events['next'])): ?>
			<table class="table table-hover">
				<?php
				foreach($events['next'] as $event): 
					$start = new JDate($event->start);
					$end =   new JDate($event->end);
					//check if this event is one day long or across multiple days
					$oneDay = ($start->day == $end->day && $start->month == $end->month);
				?>
				<tr class='clickable-row' data-href="<?php echo JRoute::_('index.php?option=com_cal&view=event&id='.$event->id); ?>">
					<td><?php echo JHTML::date($start, "d.m.");?></td>
					<td><?php echo $event->name; ?></td>
					<td><?php echo JHTML::date($start, 'H:i').' &ndash; ';
						echo $oneDay?'':JHTML::date($end, 'd.m. ');
						echo JHTML::date($end, 'H:i') ?></td>
				</tr>	
				<?php endforeach; 
					for($i = count($events['next']); $i < 5; $i++): //fill missing rows ?>
				<tr class="empty">
					<td></td>
					<td>&nbsp;</td>
					<td></td>
				</tr>	
				<?php endfor; ?>
			</table>
			<?php endif; 
				if(count($events['next']) == 5): //it's filled, use extra page for redirect ?>
			<table class="table table-hover">
				<?php
				for($i = 0; $i < 5; $i++): ?>
				<tr class="empty">
					<td></td>
					<td><?php echo $i==2?'weitere Termine LINK ENDE':'&nbsp;'; ?></td>
					<td></td>
				</tr>	
				<?php endfor; ?>
			</table>
			<?php endif; ?>
		</div>
		<div class="mod-cal-small-next" onclick="mod_cal_small_next()">
			<span class="glyphicon glyphicon-chevron-right"></span>
		</div>
	</div>
</div>
<script>
	jQuery('.mod-cal-small-inner').scrollTop(0);
	cscroll = jQuery('.mod-cal-small-active').offset().top - jQuery('.mod-cal-small-inner').offset().top;
	jQuery('.mod-cal-small-inner').scrollTop(cscroll);
	
	mod_cal_small_next_active = true;
	mod_cal_small_prev_active = true;
	
	function mod_cal_small_next() {
		if(!mod_cal_small_next_active)
			return;
		
		h = jQuery('.mod-cal-small-active').height();
		jQuery('.mod-cal-small-inner').scrollTop(jQuery('.mod-cal-small-inner').scrollTop() + h);
		
		old = jQuery('.mod-cal-small-active');
		old.next().addClass('mod-cal-small-active');
		old.removeClass('mod-cal-small-active');
		
		mod_cal_small_update();
		
	}
	function mod_cal_small_prev() {
		if(!mod_cal_small_prev_active)
			return;
		
		h = jQuery('.mod-cal-small-active').height();
		jQuery('.mod-cal-small-inner').scrollTop(jQuery('.mod-cal-small-inner').scrollTop() - h);
		
		old = jQuery('.mod-cal-small-active');
		old.prev().addClass('mod-cal-small-active');
		old.removeClass('mod-cal-small-active');
		
		mod_cal_small_update();
	}
	
	function mod_cal_small_update() {
		mod_cal_small_next_active = jQuery('.mod-cal-small-active').next().parent().hasClass('mod-cal-small-inner');
		mod_cal_small_prev_active = jQuery('.mod-cal-small-active').prev().parent().hasClass('mod-cal-small-inner');
		
		if(mod_cal_small_next_active)
			jQuery('.mod-cal-small-next').removeClass('inactive');
		else
			jQuery('.mod-cal-small-next').addClass('inactive');
		
		if(mod_cal_small_prev_active)
			jQuery('.mod-cal-small-prev').removeClass('inactive');
		else
			jQuery('.mod-cal-small-prev').addClass('inactive');
	}
	
	mod_cal_small_update();
</script>