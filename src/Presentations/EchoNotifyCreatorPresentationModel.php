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
 * @since 0.0.1
 */

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
