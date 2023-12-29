<?php

namespace MediaWiki\Extension\Adiutor\HookHandler;

use MediaWiki\ChangeTags\Hook\ChangeTagsAllowedAddHook;
use MediaWiki\ChangeTags\Hook\ChangeTagsListActiveHook;
use MediaWiki\ChangeTags\Hook\ListDefinedTagsHook;

class ChangeTagsHandler implements ChangeTagsListActiveHook, ListDefinedTagsHook, ChangeTagsAllowedAddHook {
	private string $tagName = 'adiutor';

	/**
	 * Hook to define a change tag.
	 *
	 * @param string[] &$tags Tags array
	 */
	public function onListDefinedTags( &$tags ) {
		if ( !in_array( $this->tagName,
			$tags,
			true ) ) {
			$tags[] = $this->tagName;
		}
	}

	/**
	 * Hook to mark a change tag as active.
	 *
	 * @param string[] &$tags Active tags array
	 */
	public function onChangeTagsListActive( &$tags ) {
		if ( !in_array( $this->tagName,
			$tags,
			true ) ) {
			$tags[] = $this->tagName;
		}
	}

	/**
	 * Hook to specify which change tags can be added by users.
	 *
	 * @param string[] &$allowedTags Array of tags that are allowed to be added.
	 * @param string[] $addTags Array of tags that are intended to be added.
	 * @param mixed $user The user who is performing the action, type hint removed.
	 */
	public function onChangeTagsAllowedAdd( &$allowedTags, $addTags, $user ) {
		// Allow "adiutor" tag to be manually added, check permissions as needed
		if ( !in_array( $this->tagName,
			$allowedTags,
			true ) ) {
			$allowedTags[] = $this->tagName;
		}
	}
}
