var api = new mw.Api();
var adiutorUserOptions = JSON.parse(mw.user.options.get('userjs-adiutor-extension'));
// Function to update user options
function updateOptions(options) {
	api.postWithEditToken({
		action: 'globalpreferences',
		format: 'json',
		optionname: 'userjs-adiutor-extension',
		optionvalue: JSON.stringify(options),
		formatversion: 2,
	}, function() {});
}
var adiutorUserOptionsDefault = {
	"myWorks": [],
	"myCustomSummaries": [],
	"speedyDeletion": {
		"csdSendMessageToCreator": true,
		"csdLogNominatedPages": true,
		"csdLogPageName": "Csd log page",
	},
	"articlesForDeletion": {
		"afdSendMessageToCreator": true,
		"afdLogNominatedPages": true,
		"afdLogPageName": "Afd log page",
		"afdNominateOpinionsLog": true,
		"afdOpinionLogPageName": "Afd opinion log page"
	},
	"proposedDeletion": {
		"prdSendMessageToCreator": true,
		"prdLogNominatedPages": true,
		"prdLogPageName": "Prod log page"
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
	"adiutorVersion": "v1.0.0"
};
// Get user options related to the Adiutor
var adiutorUserOptions = JSON.parse(mw.user.options.get(adiutorUserOptions));
// Check if user options are not present or empty
if(!adiutorUserOptions || Object.keys(adiutorUserOptions).length === 0) {
	// Send default user options to the server using API
	updateOptions(adiutorUserOptionsDefault);
	// Retrieve default translation data
} else {
	var hasNewOptions = false; // Flag to check if there are new options
	// Loop through the properties in adiutorUserOptionsDefault
	for(var key in adiutorUserOptionsDefault) {
		if(adiutorUserOptionsDefault.hasOwnProperty(key)) {
			// Check if the property exists in adiutorUserOptions
			if(!adiutorUserOptions.hasOwnProperty(key)) {
				// New setting found, set the flag
				hasNewOptions = true;
				adiutorUserOptions[key] = adiutorUserOptionsDefault[key]; // Add the new option
			}
		}
	}
	// Update user options if new settings are found
	if(hasNewOptions) {
		updateOptions(adiutorUserOptions);
	}
}
var xhr = new XMLHttpRequest();
xhr.open("GET", "../extensions/Adiutor/i18n/en.json", true);
xhr.onreadystatechange = function() {
	if(xhr.readyState === 4 && xhr.status === 200) {
		// Parse the JSON data once it's successfully loaded
		var messages = JSON.parse(xhr.responseText);
		console.log(messages);
		mw.messages.set(messages);
	}
};
xhr.send();
mw.loader.load("ext.Adiutor.ail");