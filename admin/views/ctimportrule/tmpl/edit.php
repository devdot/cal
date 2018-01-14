<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_cal
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');

JFactory::getDocument()->addScriptDeclaration('
	Joomla.submitbutton = function(task) {
		if (task == "ctimportrule.cancel" || document.formvalidator.isValid(document.getElementById("cal-ct-importRule-form"))) {
			Joomla.submitform(task, document.getElementById("cal-ct-importRule-form"));

			if (task !== "ctimportrule.apply") {
				window.parent.jQuery("#cal-ct-importRule-form' . $this->item->id . 'Modal").modal("hide");
			}
		}
	};
');
?>

<form action="<?php echo JRoute::_('index.php?option=com_cal&view=ctimportrule&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" class="form-validate" id="cal-ct-importRule-form">

	<?php echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>

	<div class="form-horizontal">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', JText::_('COM_CAL_EDIT_LOCATION')); ?>
		<div class="row-fluid">
			<div class="span9">
				<?php echo $this->form->renderField('rules'); ?>	
			</div>
			<div class="span3">
				<?php echo $this->form->renderField('state'); ?>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php echo JHtml::_('bootstrap.endTabSet'); ?>
	</div>
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>
