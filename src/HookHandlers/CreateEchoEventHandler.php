<?php
/**
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @file
 * @since 0.1.0
 */

namespace MediaWiki\Extension\Adiutor\HookHandlers;

use EchoAttributeManager;
use EchoUserLocator;
use MediaWiki\Extension\Adiutor\Presentations\EchoNotifyCreatorPresentationModel;
use MediaWiki\Extension\Notifications\Hooks\BeforeCreateEchoEventHook;

class CreateEchoEventHandler implements BeforeCreateEchoEventHook {
	/**
	 * Add Adiutor events to Echo
	 *
	 * @since 0.1.0
	 * @param array &$notifications array of Echo notifications
	 * @param array &$notificationCategories array of Echo notification categories
	 * @param array &$notificationIcons array of icon details
	 */
	public function onBeforeCreateEchoEvent(
		array &$notifications,
		array &$notificationCategories,
		array &$notificationIcons
	) {
		$notificationCategories[ 'moderation' ] = [
			'priority' => 3,
			'tooltip' => 'echo-pref-tooltip-moderation',
		];

		$notifications[ 'adiutor-csd-notification' ] = [
			'category' => 'adiutor',
			'group' => 'positive',
			'section' => 'alert',
			'presentation-model' => EchoNotifyCreatorPresentationModel::class,
			'bundle' => [
				'web' => true,
				'email' => true,
				'expandable' => true,
			],
			EchoAttributeManager::ATTR_LOCATORS => [
				[
					[ EchoUserLocator::class, 'locateFromEventExtra' ],
					[ 'author' ],
				],
			],
		];

		$notificationIcons[ 'adiutor-notification-icon' ] = [
			'path' => [
				'ltr' => 'Adiutor/resources/ext.adiutor.images/adiutor-notification-icon-ltr.svg',
				'rtl' => 'Adiutor/resources/ext.adiutor.images/adiutor-notification-icon-rtl.svg',
			],
		];

		$notificationIcons[ 'adiutor-notification-icon-ltr' ] = [
			'path' => 'Adiutor/resources/ext.adiutor.images/adiutor-notification-icon-ltr.svg',
		];
	}
}
