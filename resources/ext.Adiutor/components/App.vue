<template>
	<div class="cdx-docs-input-with-menu-scroll">
		<cdx-button v-model="selectedValue" class="cdx-docs-input-with-menu-scroll__input" role="combobox"
			:aria-expanded="expanded" :aria-controls="menuId" :aria-activedescendant="activeDescendant" @click="onClick"
			@blur="expanded = false" @keydown="onKeydown">
			<span class="my-icon-class--trash"></span>
 Adiutor
		</cdx-button>
		<cdx-menu :id="menuId" ref="menu" v-model:selected="selectedValue" v-model:expanded="expanded"
			:menu-items="menuItems" :footer="footer"
			:visible-item-limit="itemLimit ? parseInt(`${itemLimit}`) : null"></cdx-menu>
	</div>
</template>
<script>
const { defineComponent, ref, computed } = require('vue');
const { CdxMenu, CdxTextInput, CdxButton, useGeneratedId } = require('@wikimedia/codex');
// @vue/component
module.exports = defineComponent({
	name: 'InputWithMenuScroll',
	components: {
		CdxMenu,
		CdxButton,
		CdxTextInput
	},
	setup() {
		const menu = ref();
		const selectedValue = ref('');
		const expanded = ref(false);
		const activeDescendant = computed(() => {
			const highlightedItem = menu.value && menu.value.getHighlightedMenuItem();
			return highlightedItem ? highlightedItem.id : undefined;
		});
		const menuId = useGeneratedId('menu');
		const menuItems = [
			{ label: mw.msg('create-speedy-deletion-request'), value: 'csd' },
			{ label: mw.msg('proposed-deletion-nomination'), value: 'prd' },
			{ label: mw.msg('nominate-article-for-deletion'), value: 'afd' },
			{ label: mw.msg('page-move-request'), value: 'pmr' },
			{ label: mw.msg('page-protection-request'), value: 'rpp' },
			{ label: mw.msg('recent-changes'), value: '6' },
			{ label: mw.msg('tag-page'), value: 'tag' },
			{ label: mw.msg('copyright-violation-check'), value: 'cov' },
			{ label: mw.msg('article-info'), value: 'inf' },
		];
		const itemLimit = ref('6');

		const footer = {
			value: 'menu-footer',
			label: mw.msg('adiutor-settings')
		};

		/**
		 * Delegate most keydowns on the text input to the Menu component. This
		 * allows the Menu component to enable keyboard navigation of the menu.
		 *
		 * @param {KeyboardEvent} e The keyboard event
		 */
		function onKeydown(e) {
			// The menu component enables the space key to open and close the
			// menu. However, for text inputs with menus, the space key should
			// always insert a new space character in the input.
			if (e.key === ' ') {
				return;
			}

			// Delegate all other key events to the Menu component.
			if (menu.value) {
				menu.value.delegateKeyNavigation(e);
			}
		}

		function onClick() {
			expanded.value = true;
		}

		return {
			menu,
			selectedValue,
			expanded,
			activeDescendant,
			menuId,
			menuItems,
			footer,
			itemLimit,
			onKeydown,
			onClick
		};
	}
});
</script>