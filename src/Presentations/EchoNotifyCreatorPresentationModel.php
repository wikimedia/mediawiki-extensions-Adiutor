<?php

namespace MediaWiki\Extension\Adiutor\Presentations;

use MediaWiki\Extension\Notifications\Formatters\EchoEventPresentationModel;
use Message;

class EchoNotifyCreatorPresentationModel extends EchoEventPresentationModel {

	/**
	 * @inheritDoc
	 */
	public function getIconType(): string {
		if ( in_array( $this->language->getCode(), [ 'he', 'yi' ] ) ) {
			return 'edit-user-talk-ltr';
		}
		return 'edit-user-talk';
	}

	/**
	 * @inheritDoc
	 */
	public function getHeaderMessage(): Message {
		return $this->getMessageWithAgent( 'adiutor-notification-header-csd-requested' )
			->params( $this->event->getTitle()->getText() );
	}

	/**
	 * @inheritDoc
	 */
	public function getBodyMessage() {
		$extra = $this->event->getExtra();
		$reason = $extra['reason'] ?? '';
		$titleText = $extra['title'] ?? '';
		if ( $reason !== '' && $titleText !== '' ) {
			return $this->msg( 'adiutor-notification-body-csd-requested' )
				->params( $titleText, $reason );
		}
		return false;
	}

	/**
	 * @inheritDoc
	 */
	public function getPrimaryLink() {
		return [
			'url' => $this->event->getTitle()->getLocalURL(),
			'label' => $this->event->getTitle()->getText(),
		];
	}

}
