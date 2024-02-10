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

namespace MediaWiki\Extension\Adiutor\HookHandlers;

use MediaWiki\ChangeTags\Hook\ChangeTagsAllowedAddHook;
use MediaWiki\ChangeTags\Hook\ChangeTagsListActiveHook;
use MediaWiki\ChangeTags\Hook\ListDefinedTagsHook;

class ChangeTagsHandler implements ChangeTagsListActiveHook, ListDefinedTagsHook, ChangeTagsAllowedAddHook {
	// Make the tag name a class constant
	public const TAG_NAME = 'adiutor';

	/**
	 * Hook to define a change tag.
	 *
	 * @param string[] &$tags Tags array
	 */
	public function onListDefinedTags( &$tags ) {
		$tags[] = self::TAG_NAME;
	}

	/**
	 * Hook to mark a change tag as active.
	 *
	 * @param string[] &$tags Active tags array
	 */
	public function onChangeTagsListActive( &$tags ) {
		$tags[] = self::TAG_NAME;
	}

	/**
	 * Hook to specify which change tags can be added by users.
	 *
	 * @param string[] &$allowedTags Array of tags that are allowed to be added.
	 * @param string[] $addTags Array of tags that are intended to be added.
	 * @param mixed $user The user who is performing the action, type hint removed.
	 */
	public function onChangeTagsAllowedAdd( &$allowedTags, $addTags, $user ) {
		// If the adiutor tag is amongst those being added, add it to the list of allowed tags
		if ( in_array( self::TAG_NAME,
				$addTags,
				true ) && !in_array( self::TAG_NAME,
				$allowedTags,
				true ) ) {
			$allowedTags[] = self::TAG_NAME;
		}
	}
}
