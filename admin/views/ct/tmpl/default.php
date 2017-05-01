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
<form id="adminForm" action="?option=com_cal&view=ct" method="post" name="adminForm">
    <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10 j-toggle-main">
        <div class="span6">
            <h2><?php echo JText::_('COM_CAL_QUICKACCESS'); ?></h2>
			<div class="cal-cpanel">
				<a class="btn" onclick="Joomla.submitbutton('ct.import')">
					<span class="cal-cpanel-icon"><span class="icon icon-upload"></span></span>
					<span class="cal-cpanel-title"><?php echo JText::_("COM_CAL_CPANEL_CT_IMPORT"); ?></span>
				</a>
				<a class="btn" href="?option=com_cal&view=ct_tokenGenerator">
					<span class="cal-cpanel-icon"><span class="icon icon-wand"></span></span>
					<span class="cal-cpanel-title"><?php echo JText::_("COM_CAL_CPANEL_CT_TOKEN_GENERATOR"); ?></span>
				</a>
			</div>
        </div>
        <div class="span6">
            <?php if($this->ct->isLoggedIn()): ?>
			Login Status: OK<br>
			<?php else: ?>
			Login Status: Not logged in!
			<?php endif; ?>
        </div>
    </div>
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <?php echo JHtml::_('form.token'); ?>
</form>
