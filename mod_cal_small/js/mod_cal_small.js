mod_cal_small_next_active = true;
mod_cal_small_prev_active = true;
	
function mod_cal_small() {
	jQuery('.mod-cal-small-inner').scrollTop(0);
	cscroll = jQuery('.mod-cal-small-active').offset().top - jQuery('.mod-cal-small-inner').offset().top;
	jQuery('.mod-cal-small-inner').scrollTop(cscroll);
}
function mod_cal_small_next() {
	if(!mod_cal_small_next_active)
		return;

	h = jQuery('.mod-cal-small-active').height();
	jQuery('.mod-cal-small-inner').animate({scrollTop: jQuery('.mod-cal-small-inner').scrollTop() + h}, 500);

	old = jQuery('.mod-cal-small-active');
	old.next().addClass('mod-cal-small-active');
	old.removeClass('mod-cal-small-active');

	mod_cal_small_update();

}
function mod_cal_small_prev() {
	if(!mod_cal_small_prev_active)
		return;

	h = jQuery('.mod-cal-small-active').height();
	jQuery('.mod-cal-small-inner').animate({scrollTop: jQuery('.mod-cal-small-inner').scrollTop() - h}, 500);

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
	
	
