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

JHtml::_('behavior.formvalidator');
JFactory::getDocument()->addScriptDeclaration('
	Joomla.submitbutton = function(task) {
		if (document.formvalidator.isValid(document.getElementById("adminForm"))) {
			Joomla.submitform(task, document.getElementById("adminForm"));
		}
	};');

?>
<form id="adminForm" action="?option=com_cal&view=ct_tokenGenerator" method="post" name="adminForm">
    <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10 j-toggle-main">
		<?php echo JText::_('COM_CAL_CT_TOKEN_GENERATOR_DESC'); ?>
        <?php echo $this->form->renderField('url'); ?>
		<?php echo $this->form->renderField('email'); ?>
		<?php echo $this->form->renderField('password'); ?>
    </div>
    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?>
</form>
