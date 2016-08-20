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

?>
<form id="adminForm" action="?option=com_cal&view=event" method="post" name="adminForm">
    <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10 j-toggle-main">
        <div class="span6">
            <h2>Upcoming Events</h2>

        </div>
        <div class="span6">
            <h2>New Events</h2>
        </div>
    </div>
    <input name="task" value="" type="hidden">
</form>
