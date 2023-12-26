<?php

namespace MediaWiki\Extension\Adiutor\HookHandler;

use MediaWiki\ChangeTags\Hook\ListDefinedTagsHook;

class ListDefinedTagsHandler implements ListDefinedTagsHook {
	/**
	 * Hook to define a change tag.
	 *
	 * @param string[] &$tags Tags array
	 */
	public function onListDefinedTags( &$tags ) {
		$tagName = 'Adiutor';
		if ( !in_array( $tagName,
			$tags,
			true ) ) {
			$tags[] = $tagName;
		}
	}
}
