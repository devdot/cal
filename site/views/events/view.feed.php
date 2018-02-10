<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_cal
 */

defined('_JEXEC') or die;

/**
 * Used to display the rss feed of events
 *
 * @since  1.5
 */
class CalViewEvents extends JViewLegacy
{
	/**
	 * @var    string  The name of the view to link individual items to
	 * @since  3.2
	 */
	protected $viewName = 'event';

	public function display($tpl = null) {
		// from CategoryFeedView
		
		$app      = JFactory::getApplication();
		$document = JFactory::getDocument();

		$extension = $app->input->getString('option');
		$contentType = $extension . '.' . $this->viewName;

		$createdField = null;

		$document->link = JRoute::_('index.php?option=com_cal&view=events');

		$app->input->set('limit', $app->get('feed_limit'));
		$siteEmail        = $app->get('mailfrom');
		$fromName         = $app->get('fromname');
		$feedEmail        = $app->get('feed_email', 'none');
		$document->editor = $fromName;
		
		if ($feedEmail !== 'none') {
			$document->editorEmail = $siteEmail;
		}

		// Get some data from the model
		$items    = $this->get('Items');
		
		foreach ($items as $item) {
			// Strip html from feed item title
			$title = $this->escape($item->name);
			$title = JHTML::date($item->start, "d.m.Y H:i").'Uhr: '.html_entity_decode($title, ENT_COMPAT, 'UTF-8');

			// URL link to article	
			$link   = JRoute::_('index.php?option=com_cal&view=event&id='.$item->id);

			// build the description
			$desc = $item->introtext.$item->fulltext;
			
			// check for recurring children
			if($desc == '' && $item->recurring_id != NULL)
				$desc = $item->parent_introtext.$item->parent_fulltext;
			
			$desc .= '<p>Datum: '.JHTML::date($item->start, "d.m.Y").'<br>';
			$desc .= 'Zeit: '.JHTML::date($item->start, "H:i").'<br>';
			$desc .= 'Veranstaltungsort: '.$item->location_name.'<br>';
			$desc .= 'Kategorie: '.$item->cat_name.'</p>';
			
			$date = date('r', strtotime($item->created));

			// Load individual item creator class.
			$feeditem              = new \JFeedItem;
			$feeditem->title       = $title;
			$feeditem->link        = $link;
			$feeditem->description = $desc;
			$feeditem->date        = $date;
			$feeditem->category    = $item->cat_name;
			//$feeditem->author      = $author;

			// We don't have the author email so we have to use site in both cases.
			if ($feedEmail === 'site') {
				$feeditem->authorEmail = $siteEmail;
			}
			elseif ($feedEmail === 'author') {
				$feeditem->authorEmail = $item->author_email;
			}

			// Loads item information into RSS array
			$document->addItem($feeditem);
		}
	}
	
}
