<?php

namespace MediaWiki\Extension\Adiutor\HookHandler;

use EchoAttributeManager;
use EchoUserLocator;
use MediaWiki\Extension\Adiutor\EchoNotifyCreatorPresentationModel;
use MediaWiki\Extension\Notifications\Hooks\BeforeCreateEchoEventHook;

/**
 * CreateEchoEventHandler class
 */
class CreateEchoEventHandler implements BeforeCreateEchoEventHook {
	/**
	 * Add Adiutor events to Echo
	 *
	 * @param array &$notifications array of Echo notifications
	 * @param array &$notificationCategories array of Echo notification categories
	 * @param array &$icons array of icon details
	 */
	public function onBeforeCreateEchoEvent(
		array &$notifications, array &$notificationCategories, array &$icons
	) {
		$notificationCategories['moderation'] = [
			'priority' => 3,
			'tooltip' => 'echo-pref-tooltip-moderation',
		];

		$notifications['adiutor-csd-notification'] = [
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
					[ 'author' ]
				],
			],
		];

		$icons['adiutor-notification-icon'] = [
			'path' => [
				'ltr' => 'Adiutor/resources/ext.adiutor.images/adiutor-notification-icon-ltr.svg',
				'rtl' => 'Adiutor/resources/ext.adiutor.images/adiutor-notification-icon-rtl.svg'
			]
		];

		$icons['adiutor-notification-icon-ltr'] = [
			'path' => 'Adiutor/resources/ext.adiutor.images/adiutor-notification-icon-ltr.svg'
		];
	}
}
