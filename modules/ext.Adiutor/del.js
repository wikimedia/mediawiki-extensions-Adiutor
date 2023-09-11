var api = new mw.Api();
var mwConfig = mw.config.get(["wgArticleId", "wgPageName", "wgNamespaceNumber", "wgUserName"]);
var adiutorUserOptions = {
	"myWorks": [],
	"myCustomSummaries": [],
	"speedyDeletion": {
		"csdSendMessageToCreator": true,
		"csdLogNominatedPages": true,
		"csdLogPageName": "HS günlüğü",
	},
	"articlesForDeletion": {
		"afdSendMessageToCreator": true,
		"afdLogNominatedPages": true,
		"afdLogPageName": "SAS günlüğü",
		"afdNominateOpinionsLog": true,
		"afdOpinionLogPageName": "SAS görüş günlüğü"
	},
	"proposedDeletion": {
		"prdSendMessageToCreator": true,
		"prdLogNominatedPages": true,
		"prdLogPageName": "BS günlüğü"
	},
	"status": {
		"showMyStatus": true,
		"myStatus": "active"
	},
	"stats": {
		"csdRequests": 0,
		"afdRequests": 0,
		"prodRequests": 0,
		"blockRequests": 0,
		"userWarnings": 0,
		"pageTags": 0,
	},
	"inlinePageInfo": true,
	"showEditSummaries": true,
	"adiutorVersion": "v1.2.6"
};
var casdReason, csdSummary, notificationMessage, articleAuthor;
var csdOptions = [];
var casdReasons = [];
var saltCsdSummary = '';
var pageTitle = mw.config.get("wgPageName").replace(/_/g, " ");

function fetchApiData(callback) {
	var api = new mw.Api();
	api.get({
		action: "query",
		prop: "revisions",
		titles: "MediaWiki:Adiutor-CSD.json",
		rvprop: "content",
		formatversion: 2
	}).done(function(data) {
		var content = data.query.pages[0].revisions[0].content;
		try {
			var jsonData = JSON.parse(content);
			callback(jsonData);
		} catch(error) {
			// Handle JSON parsing error
			mw.notify('Failed to parse JSON data from API.', {
				title: mw.msg('operation-failed'),
				type: 'error'
			});
		}
	}).fail(function() {
		// Handle API request failure
		mw.notify('Failed to fetch data from the API.', {
			title: mw.msg('operation-failed'),
			type: 'error'
		});
		// You may choose to stop code execution here
	});
}
fetchApiData(function(jsonData) {
	if(!jsonData) {
		// Handle a case where jsonData is empty or undefined
		mw.notify('MediaWiki:Gadget-Adiutor-UBM.json data is empty or undefined.', {
			title: mw.msg('operation-failed'),
			type: 'error'
		});
		// You may choose to stop code execution here
		return;
	}
	api.get({
		action: "query",
		format: "json",
		titles: mwConfig.wgPageName
	}).done(function(data) {
		var pages = data.query.pages;
		var pageId = Object.keys(pages)[0];
		if(pageId !== "-1") {
			api.get({
				action: 'query',
				list: 'logevents',
				leaction: 'delete/delete',
				letprop: 'delete',
				letitle: mwConfig.wgPageName
			}).done(function(data) {
				if(data.query.logevents) {
					revDelCount = data.query.logevents.length;
				} else {
					revDelCount = 0;
				}
				var speedyDeletionReasons = jsonData.speedyDeletionReasons;
				var talkPagePrefix = jsonData.talkPagePrefix;
				var apiPostSummaryforTalkPage = jsonData.apiPostSummaryforTalkPage;

				function csdAdminProcessDialog(config) {
					csdAdminProcessDialog.super.call(this, config);
				}
				OO.inheritClass(csdAdminProcessDialog, OO.ui.ProcessDialog);
				csdAdminProcessDialog.static.title = mwConfig.wgPageName;
				csdAdminProcessDialog.static.name = 'csdAdminProcessDialog';
				// An action set that uses modes ('edit' and 'help' mode, in this example).
				csdAdminProcessDialog.static.actions = [{
					action: 'continue',
					modes: 'edit',
					label: mw.msg('confirm-action'),
					flags: ['primary', 'destructive']
				}, {
					action: 'help',
					modes: 'edit',
					label: mw.msg('help')
				}, {
					modes: 'edit',
					label: mw.msg('cancel'),
					flags: 'safe'
				}];
				csdAdminProcessDialog.prototype.initialize = function() {
					csdAdminProcessDialog.super.prototype.initialize.apply(this, arguments);
					var i, reason, checkboxWidget, fieldLayout;
					var selectedNamespace = null;
					if(mw.config.get('wgIsRedirect')) {
						selectedNamespace = speedyDeletionReasons.find(reason => reason.namespace === 'redirect');
						nameSpaceDeletionReasons = new OO.ui.FieldsetLayout({
							label: selectedNamespace.name
						});
						for(i = 0; i < selectedNamespace.reasons.length; i++) {
							reason = selectedNamespace.reasons[i];
							checkboxWidget = new OO.ui.CheckboxInputWidget({
								value: reason.value,
								data: reason.data,
								selected: false
							});
							fieldLayout = new OO.ui.FieldLayout(checkboxWidget, {
								label: reason.label,
								align: 'inline',
								help: reason.help
							});
							nameSpaceDeletionReasons.addItems([fieldLayout]);
						}
					} else {
						var NS_MAIN = 0,
							NS_USER = 2,
							NS_USER_TALK = 3,
							NS_FILE = 6,
							NS_TEMPLATE = 10,
							NS_CATEGORY = 14;
						switch(mwConfig.wgNamespaceNumber) {
							case NS_MAIN:
							case NS_FILE:
							case NS_CATEGORY:
							case NS_USER:
							case NS_USER_TALK:
							case NS_TEMPLATE:
								selectedNamespace;
								if(mwConfig.wgNamespaceNumber === NS_USER || mwConfig.wgNamespaceNumber === NS_USER_TALK) {
									selectedNamespace = speedyDeletionReasons.find(reason => reason.namespace === NS_USER);
								} else {
									selectedNamespace = speedyDeletionReasons.find(reason => reason.namespace === mwConfig.wgNamespaceNumber);
								}
								if(selectedNamespace) {
									nameSpaceDeletionReasons = new OO.ui.FieldsetLayout({
										label: selectedNamespace.name
									});
									for(i = 0; i < selectedNamespace.reasons.length; i++) {
										reason = selectedNamespace.reasons[i];
										checkboxWidget = new OO.ui.CheckboxInputWidget({
											value: reason.value,
											data: reason.data,
											selected: false
										});
										fieldLayout = new OO.ui.FieldLayout(checkboxWidget, {
											label: reason.label,
											align: 'inline',
											help: reason.help
										});
										nameSpaceDeletionReasons.addItems([fieldLayout]);
									}
								} else {
									nameSpaceDeletionReasons = new OO.ui.FieldsetLayout({});
									nameSpaceDeletionReasons.addItems([
										new OO.ui.FieldLayout(new OO.ui.MessageWidget({
											type: 'warning',
											inline: true,
											label: new OO.ui.HtmlSnippet('<strong>' + mw.msg('no-namespace-reason-for-csd-title') + '</strong><br><small>' + mw.msg('no-namespace-reason-for-csd') + '</small>')
										})),
									]);
								}
								break;
							default:
								nameSpaceDeletionReasons = new OO.ui.FieldsetLayout({});
								nameSpaceDeletionReasons.addItems([
									new OO.ui.FieldLayout(new OO.ui.MessageWidget({
										type: 'warning',
										inline: true,
										label: new OO.ui.HtmlSnippet('<strong>' + mw.msg('no-namespace-reason-for-csd-title') + '</strong><br><small>' + mw.msg('no-namespace-reason-for-csd') + '</small>')
									})),
								]);
								break;
						}
					}
					selectedNamespaceForGeneral = null;
					for(i = 0; i < speedyDeletionReasons.length; i++) {
						if(speedyDeletionReasons[i].namespace === 'general') {
							selectedNamespaceForGeneral = {
								name: speedyDeletionReasons[i].name,
								reasons: speedyDeletionReasons[i].reasons
							};
							break;
						}
					}
					copyVioInput = new OO.ui.TextInputWidget({
						placeholder: mw.msg('copyright-infringing-page'),
						value: '',
						icon: 'link',
						data: 'COV',
						classes: ['adiutor-copvio-input'],
					});
					copyVioInput.$element.css({
						'margin-top': '10px',
						'margin-bottom': '10px'
					});
					copyVioInput.$element.hide();
					isCopyVio = false;
					generalReasons = new OO.ui.FieldsetLayout({
						label: selectedNamespaceForGeneral.name
					});
					for(i = 0; i < selectedNamespaceForGeneral.reasons.length; i++) {
						reason = selectedNamespaceForGeneral.reasons[i];
						checkboxWidget = new OO.ui.CheckboxInputWidget({
							value: reason.value,
							data: reason.data,
							selected: false
						});
						if(reason.value === 'G9') {
							fieldLayout = new OO.ui.FieldLayout(checkboxWidget, {
								label: reason.label,
								align: 'inline',
								help: reason.help
							});
							fieldLayout.$element.append(copyVioInput.$element);
							copyVioInput.$element.hide(); // Hide it initially
						} else {
							fieldLayout = new OO.ui.FieldLayout(checkboxWidget, {
								label: reason.label,
								align: 'inline',
								help: reason.help
							});
						}
						generalReasons.addItems([fieldLayout]);
					}
					selectedNamespaceForOthers = null;
					for(i = 0; i < speedyDeletionReasons.length; i++) {
						if(speedyDeletionReasons[i].namespace === 'other') {
							selectedNamespaceForOthers = {
								name: speedyDeletionReasons[i].name,
								reasons: speedyDeletionReasons[i].reasons
							};
							break;
						}
					}
					otherReasons = new OO.ui.FieldsetLayout({
						label: selectedNamespaceForOthers.name
					});
					for(i = 0; i < selectedNamespaceForOthers.reasons.length; i++) {
						reason = selectedNamespaceForOthers.reasons[i];
						checkboxWidget = new OO.ui.CheckboxInputWidget({
							value: reason.value,
							data: reason.data,
							selected: false
						});
						fieldLayout = new OO.ui.FieldLayout(checkboxWidget, {
							label: reason.label,
							align: 'inline',
							help: reason.help
						});
						otherReasons.addItems([fieldLayout]);
					}
					generalReasons.$element.on('click', function(item) {
						if(item.target.value === 'G9') {
							copyVioInput.$element.show();
						}
					});
					var left_panel = new OO.ui.PanelLayout({
						$content: [nameSpaceDeletionReasons.$element],
						classes: ['one'],
						scrollable: false,
					});
					var right_panel = new OO.ui.PanelLayout({
						$content: [generalReasons.$element, otherReasons.$element],
						classes: ['two'],
						scrollable: false,
					});
					var stack = new OO.ui.StackLayout({
						items: [left_panel, right_panel],
						continuous: true,
						classes: ['adiutor-csd-modal-container']
					});
					this.panel1 = new OO.ui.PanelLayout({
						padded: true,
						expanded: false,
						classes: ['adiutor-csd-modal-container-panel-1']
					});
					if(revDelCount >= "1") {
						var deletionMessage = mw.msg('page-deletion-count-warning', revDelCount);
						var deletionMessageWithLink = deletionMessage.replace(/\$2/g, '<a href="/wiki/Special:Log?type=delete&user=&page=' + mwConfig.wgPageName + '">' + mw.msg('log') + '</a>');
						var headerBarRevDel = new OO.ui.MessageWidget({
							type: 'warning',
							label: new OO.ui.HtmlSnippet(deletionMessageWithLink)
						});
						headerBarRevDel.$element.css({
							'margin-bottom': '20px',
						});
						this.panel1.$element.append(headerBarRevDel.$element, stack.$element);
					} else {
						this.panel1.$element.append(stack.$element);
					}
					this.stackLayout = new OO.ui.StackLayout({
						items: [this.panel1],
						classes: ['adiutor-csd-modal-container-user-panel']
					});
					this.$body.append(this.stackLayout.$element);
				};
				csdAdminProcessDialog.prototype.getSetupProcess = function(data) {
					return csdAdminProcessDialog.super.prototype.getSetupProcess.call(this, data).next(function() {
						this.actions.setMode('edit');
					}, this);
				};
				csdAdminProcessDialog.prototype.getActionProcess = function(action) {
					if(action === 'help') {
						this.actions.setMode('help');
						window.open('https://meta.wikimedia.org/wiki/Adiutor', '_blank');
					} else if(action === 'back') {
						this.actions.setMode('edit');
						this.stackLayout.setItem(this.panel1);
					} else if(action === 'continue') {
						var dialog = this;
						return new OO.ui.Process(function() {
							nameSpaceDeletionReasons.items.forEach(function(Reason) {
								if(Reason.fieldWidget.selected) {
									casdReasons.push({
										value: Reason.fieldWidget.value,
										data: Reason.fieldWidget.data,
										selected: Reason.fieldWidget.selected
									});
								}
							});
							generalReasons.items.forEach(function(Reason) {
								if(Reason.fieldWidget.selected) {
									casdReasons.push({
										value: Reason.fieldWidget.value,
										data: Reason.fieldWidget.data,
										selected: Reason.fieldWidget.selected
									});
								}
							});
							var CopVioURL = copyVioInput.value ? ' | ' + mw.msg('copyright-violation') + ':' + copyVioInput.value : '';
							if(casdReasons.length > 0) {
								if(casdReasons.length > 1) {
									saltCsdSummary = casdReasons.map(function(reason) {
										return '[[VP:HS#' + reason.value + ']]';
									}).join(', ');
									saltCsdSummary = saltCsdSummary.replace(/,(?=[^,]*$)/, ' ve');
								} else {
									saltCsdSummary = csdSummary = casdReasons[0].data;
								}
								saltCsdSummary += CopVioURL;
								csdSummary = saltCsdSummary;
								api.postWithToken('csrf', {
									action: 'delete',
									title: mwConfig.wgPageName,
									reason: csdSummary,
									tags: 'Adiutor',
									format: 'json'
								}).done(function() {
									api.postWithToken('csrf', {
										action: 'delete',
										title: talkPagePrefix + mwConfig.wgPageName,
										reason: apiPostSummaryforTalkPage,
										tags: 'Adiutor',
										format: 'json'
									}).done(function() {});
									dialog.close();
									location.reload();
								});
							} else {
								mw.notify(mw.message('select-speedy-deletion-reason').text(), {
									title: mw.msg('warning'),
									type: 'error'
								});
							}
						});
					}
					return csdAdminProcessDialog.super.prototype.getActionProcess.call(this, action);
				};
				var CsdWindowManager = new OO.ui.WindowManager();
				$(document.body).append(CsdWindowManager.$element);
				var dialog = new csdAdminProcessDialog({
					size: 'larger',
					classes: 'adiutor-user-dashboard-admin-csd-reason-dialog'
				});
				CsdWindowManager.addWindows([dialog]);
				CsdWindowManager.openWindow(dialog);
			});
		} else {
			mw.notify(mw.message('page-not-found').text(), {
				title: mw.msg('warning'),
				type: 'error'
			});
		}
	}).catch(error => {
		console.error("API error:", error);
	});
});