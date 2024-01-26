<?php

namespace MediaWiki\Extension\Adiutor\Hooks;

use MediaWiki\ChangeTags\Hook\ChangeTagsAllowedAddHook;
use MediaWiki\ChangeTags\Hook\ChangeTagsListActiveHook;
use MediaWiki\ChangeTags\Hook\ListDefinedTagsHook;

class ChangeTagsHandler implements ChangeTagsListActiveHook, ListDefinedTagsHook, ChangeTagsAllowedAddHook {
	// Make the tag name a class constant
	private const TAG_NAME = 'adiutor';

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
