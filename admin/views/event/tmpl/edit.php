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

$isChild = $this->item->recurring_id != 0; //if the recurring id is 0, it can't be a recurring child
$isParent = !empty($this->item->recurring_schedule); //parent have a schedule set
$isRecurring = $isChild || $isParent;

JFactory::getDocument()->addScriptDeclaration('
	Joomla.submitbutton = function(task) {
		if(task != "event.cancel") {
			start = new Date(document.getElementById("jform_start").value);
			end = new Date(document.getElementById("jform_end").value);
			if(start.getTime() >= end.getTime()) {
				alert("'.JText::_('COM_CAL_ERROR_START_AFTER_END').'");
				return false;
			}
		}
		if (task == "event.cancel" || document.formvalidator.isValid(document.getElementById("cal-event-form"))) {
			jQuery("#permissions-sliders select").attr("disabled", "disabled");
			' . $this->form->getField('articletext')->save() . '
			Joomla.submitform(task, document.getElementById("cal-event-form"));

			if (task !== "event.apply") {
				window.parent.jQuery("#cal-event-form' . (int) $this->item->id . 'Modal").modal("hide");
			}
		}
	};
	function cal_recurring_make() {
		value = parseInt(document.getElementById("jform_make_recurring").value);
		if(value == 1)
			if(confirm("'.JText::_('COM_CAL_FIELD_MAKE_RECURRING_DESC').'"))
				Joomla.submitbutton("event.apply");
			else
				document.getElementById("jform_make_recurring").checked = false;
	}
	function cal_recurring_stop() {
		value = parseInt(document.getElementById("jform_stop_recurring").value);
		if(value == 1)
			if(confirm("'.JText::_('COM_CAL_FIELD_STOP_RECURRING_DESC').'"))
				Joomla.submitbutton("event.apply");
			else
				document.getElementById("jform_stop_recurring").checked = false;
	}
');

//http://www.codingace.com/joomla-jform-field-types

?>

<form action="<?php echo JRoute::_('index.php?option=com_cal&view=event&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" class="form-validate" id="cal-event-form">

	<?php echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>

	<div class="form-horizontal">
		<?php if($this->item->id == 0): 
			echo $this->form->renderField('make_recurring');
		elseif($isChild):
			echo JText::_('COM_CAL_RECURRING_ISCHILD');
			echo $this->form->renderField('stop_recurring');
		elseif($isParent):
			echo JText::_('COM_CAL_RECURRING_ISPARENT');
		else:
			echo JText::_('COM_CAL_RECURRING_ISNOT');
			echo $this->form->renderField('make_recurring');
		endif;
		?>
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', JText::_('COM_CAL_EDIT_EVENT')); ?>
		<div class="row-fluid">
			<div class="span12">
				<div class="row-fluid form-horizontal-desktop">
					<div class="span9">
						<div class="span12">
							<div class="span6">
								<?php echo $this->form->renderField('start'); ?>
								<?php echo $this->form->renderField('end'); ?>
							</div>
							<div class="span6 form-vertical">
								<?php echo $this->form->renderField('location_id'); ?>
								<?php echo $this->form->renderField('link'); ?>
							</div>
						</div>
						<fieldset class="adminform">
							<?php echo $this->form->getInput('articletext'); ?>
						</fieldset>
					</div>
					<div class="span3 form-vertical">
						<?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>
					</div>
				</div>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php if($isParent): 
		echo JHtml::_('bootstrap.addTab', 'myTab', 'recurring', JText::_('COM_CAL_EDIT_RECURRING')); ?>
		<div class="row-fluid form-vertical">
			<div class="span6">
				<?php echo $this->form->renderField('recurring_selector'); ?>
				<?php echo $this->form->renderField('recurring_end'); ?>
				<input type="hidden" name="jform[recurring_schedule]" value="<?php echo $this->item->recurring_schedule; ?>" />
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); 
		endif; ?>
		
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'resources', JText::_('COM_CAL_EDIT_RESOURCES')); ?>
		<div class="row-fluid form-vertical">
			<div class="span6">
				<?php echo $this->form->renderField('resources'); ?>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
		
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'images', JText::_('COM_CAL_EDIT_IMAGES')); ?>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span6">
				<?php echo $this->form->renderField('images'); ?>
				<?php foreach ($this->form->getGroup('images') as $field) : ?>
					<?php echo $field->renderField(); ?>
				<?php endforeach; ?>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
		
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'publishing', JText::_('COM_CAL_FIELDSET_PUBLISHING')); ?>
			<div class="row-fluid form-horizontal-desktop">
				<div class="span6">
					<?php echo JLayoutHelper::render('joomla.edit.publishingdata', $this);
						if($isChild)
							echo $this->form->renderField('recurring_id');
					?>
				</div>
				<div class="span6">
					<?php echo $this->form->renderField('metakey');
						echo $this->form->renderField('metadesc');?>
				</div>
			</div>
			<?php echo JHtml::_('bootstrap.endTab'); ?>


		<?php echo JHtml::_('bootstrap.endTabSet'); ?>
	</div>
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>
