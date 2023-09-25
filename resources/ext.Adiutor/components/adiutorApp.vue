<template>
	<div>
		<component :is="activeComponent" :key="activeComponentKey" />
	</div>
</template>
<script>
const requestPageProtection = require('./requestPageProtection.vue');
const requestPageMove = require('./requestPageMove.vue');
const articleTagging = require('./articleTagging.vue');
const createSpeedyDeletion = require('./createSpeedyDeletion.vue');
const deletionPropose = require('./deletionPropose.vue');
const articleForDeletion = require('./articleForDeletion.vue');
const portletLinks = [
  {
    id: 't-request-speedy-deletion',
    label: 'Request speedy deletion',
    action: 'Request speedy deletion',
    key: 'rsd',
    namespaces: [-1, 0, 1, 2, 3, 4, 5, 6, 7, 14, 10, 11, 100, 101, 102, 103, 828, 829],
  },
  {
    id: 't-propose-deletion',
    label: 'Propose deletion',
    action: 'Propose deletion',
    key: 'pd',
    namespaces: [-1, 0, 1, 2, 3, 4, 5, 6, 7, 14, 10, 11, 100, 101, 102, 103, 828, 829],
  },
  {
    id: 't-article-for-deletion',
    label: 'Article for deletion',
    action: 'Article for deletion',
    key: 'afd',
    namespaces: [-1, 0, 1, 2, 3, 4, 5, 6, 7, 14, 10, 11, 100, 101, 102, 103, 828, 829],
  },
  {
    id: 't-request-protection',
    label: 'Request protection',
    action: 'Request protection',
    key: 'pr',
    namespaces: [-1, 0, 1, 2, 3, 4, 5, 6, 7, 14, 10, 11, 100, 101, 102, 103, 828, 829],
  },
  {
    id: 't-request-page-move',
    label: 'Request page move',
    action: 'Request page move',
    key: 'rpm',
    namespaces: [-1, 0, 1, 2, 3, 4, 5, 6, 7, 14, 10, 11, 100, 101, 102, 103, 828, 829],
  },
  {
    id: 't-tag-article',
    label: 'Tag article',
    action: 'Tag article',
    key: 'tag',
    namespaces: [-1, 0, 1, 2, 3, 4, 5, 6, 7, 14, 10, 11, 100, 101, 102, 103, 828, 829],
  },
  {
    id: 't-report-user',
    label: 'Report user',
    action: 'Report user',
    key: 'rep',
    namespaces: [2, 3],
  },
];

portletLinks.forEach(link => {
  if (link.namespaces.includes(mw.config.get('wgNamespaceNumber'))) {
    mw.util.addPortletLink('p-cactions', '#', link.label, link.id, link.action, link.key);
  }
});

module.exports = {
	name: 'adiutorInterfaceLoader',
	data() {
		return {
			activeComponent: null,
			activeComponentKey: 0,
		};
	},
	components: {
		requestPageProtection,
		requestPageMove,
		createSpeedyDeletion,
		deletionPropose,
		articleForDeletion,
		articleTagging,
	},
	methods: {
		showComponent(componentName) {
			if (componentName === this.activeComponent) {
				this.activeComponentKey++;
			} else {
				this.activeComponent = componentName;
			}
		},
	},
	created() {
		this.$nextTick(() => {
			const speedyDeletionRequestLink = document.querySelector('#t-request-speedy-deletion');
			const proposeDeletionLink = document.querySelector('#t-propose-deletion');
			const articleForDeletionLink = document.querySelector('#t-article-for-deletion');
			const requestProtectionLink = document.querySelector('#t-request-protection');
			const requestPageMoveLink = document.querySelector('#t-request-page-move');
			const tagArticleLink = document.querySelector('#t-tag-article');
			if (speedyDeletionRequestLink) {
				speedyDeletionRequestLink.addEventListener('click', () => {
					this.showComponent('createSpeedyDeletion');
				});
			}
			if (proposeDeletionLink) {
				proposeDeletionLink.addEventListener('click', () => {
					this.showComponent('deletionPropose');
				});
			}
			if (articleForDeletionLink) {
				articleForDeletionLink.addEventListener('click', () => {
					this.showComponent('articleForDeletion');
				});
			}
			if (requestProtectionLink) {
				requestProtectionLink.addEventListener('click', () => {
					this.showComponent('requestPageProtection');
				});
			}
			if (requestPageMoveLink) {
				requestPageMoveLink.addEventListener('click', () => {
					this.showComponent('requestPageMove');
				});
			}
			if (tagArticleLink) {
				tagArticleLink.addEventListener('click', () => {
					this.showComponent('articleTagging');
				});
			}
		});
	},
};
</script>