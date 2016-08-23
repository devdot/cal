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
	Joomla.submitbutton = function(task)
	{
		if (task == "location.cancel" || document.formvalidator.isValid(document.getElementById("cal-location-form")))
		{
			' . $this->form->getField("desc")->save() . '
			Joomla.submitform(task, document.getElementById("cal-location-form"));

			if (task !== "location.apply")
			{
				window.parent.jQuery("#cal-location-form' . $this->item->ID . 'Modal").modal("hide");
			}
		}
	};
');

//https://www.youtube.com/watch?v=sYd7A9Nf_SI
?>

<form action="<?php echo JRoute::_('index.php?option=com_cal&view=location&layout=edit&ID=' . (int) $this->item->ID); ?>" method="post" name="adminForm" class="form-validate" id="cal-location-form">

	<?php echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>

	<div class="form-horizontal">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', JText::_('COM_CAL_EDIT_LOCATION')); ?>
		<div class="row-fluid">
			<div class="span9">
				<div class="row-fluid form-horizontal-desktop">
					<div class="span6">
						<?php echo $this->form->renderField('addrStreet'); ?>
						<?php echo $this->form->renderField('addrZip'); ?>
						<?php echo $this->form->renderField('addrCity'); ?>
						<?php echo $this->form->renderField('addrCountry'); ?>
					</div>
					<div class="span6">
						<?php echo $this->form->renderField('link'); ?>
						<?php echo $this->form->renderField('geoX'); ?>
						<?php echo $this->form->renderField('geoY'); ?>
					</div>
				</div>
			</div>
			<div class="span3">
				<?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'misc', JText::_('COM_CAL_EDIT_MISC')); ?>
		<div class="row-fluid form-horizontal-desktop">
			<?php echo $this->form->renderField('ID'); ?>
			<div class="form-vertical">
				<?php echo $this->form->renderField('desc'); ?>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>


		<?php echo JHtml::_('bootstrap.endTabSet'); ?>
	</div>
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>
