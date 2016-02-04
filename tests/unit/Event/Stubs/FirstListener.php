<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Tests\Unit\Event\Stubs;

use Joomla\Event\Event;

/**
 * A listener used to test the triggerEvent method in the dispatcher.
 * It will be added in first position.
 *
 * @since  1.0
 */
class FirstListener
{
	/**
	 * Listen to `fooBar`.
	 *
	 * @param   Event  $event  The event.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function fooBar(Event $event)
	{
	}

	/**
	 * Listen to onSomething.
	 *
	 * @param   Event  $event  The event.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function onSomething(Event $event)
	{
		$event->setArgument('listeners', array('first'));
	}
}
