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
		if (task == "resource.cancel" || document.formvalidator.isValid(document.getElementById("cal-resource-form"))) {
			' . $this->form->getField("description")->save() . '
			Joomla.submitform(task, document.getElementById("cal-resource-form"));

			if (task !== "resource.apply") {
				window.parent.jQuery("#cal-resource-form' . $this->item->id . 'Modal").modal("hide");
			}
		}
	};
	function cal_resource_type() {
		type = document.getElementById("jform_cal_resource_type_field").value;
		if(type == 3)
			jQuery("#cal_resource_user").show();
		else
			jQuery("#cal_resource_user").hide();
		
		if(type == 4)
			jQuery("#cal_resource_usergroup").show();
		else
			jQuery("#cal_resource_usergroup").hide();
		return type;
	}
');

?>

<form action="<?php echo JRoute::_('index.php?option=com_cal&view=resource&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" class="form-validate" id="cal-resource-form">

	<?php echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>

	<div class="form-horizontal">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', JText::_('COM_CAL_RESOURCE_EDIT')); ?>
		<div class="row-fluid">
			<div class="span12">
				<div class="row-fluid form-horizontal-desktop">
					<div class="span6">
						<?php echo $this->form->renderField('catid'); ?>
					</div>
					<div class="span6">
						<?php echo $this->form->renderField('type'); ?>
						<div id="cal_resource_user">
							<?php echo $this->form->renderField('user_id'); ?>
						</div>
						<div id="cal_resource_usergroup">
							<?php echo $this->form->renderField('usergroup_id'); ?>
						</div>
					</div>
					<script>cal_resource_type();</script>
				</div>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'misc', JText::_('COM_CAL_EDIT_MISC')); ?>
		<div class="row-fluid form-horizontal-desktop">
			<?php echo $this->form->renderField('id'); ?>
			<div class="form-vertical">
				<?php echo $this->form->renderField('description'); ?>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>


		<?php echo JHtml::_('bootstrap.endTabSet'); ?>
	</div>
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>
