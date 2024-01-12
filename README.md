# Adiutor

https://www.mediawiki.org/wiki/Extension:Adiutor

[![Build Status](https://travis-ci.org/wikimedia/mediawiki-extensions-Adiutor.svg?branch=master)](https://travis-ci.org/wikimedia/mediawiki-extensions-Adiutor)
[![Coverage Status](https://coveralls.io/repos/github/wikimedia/mediawiki-extensions-Adiutor/badge.svg?branch=master)](https://coveralls.io/github/wikimedia/mediawiki-extensions-Adiutor?branch=master)

Adiutor is a MediaWiki extension to moderate, triage, and maintain content tasks on Wikipedia. Utilizing the advanced
capabilities of the Codex design system, specifically developed for Wikimedia, along with the all-purpose features of
Vue.js, this extension enables Wikipedia users a user-friendly and convenient interface for conducting a wide range of
tasks. It implements content triage methods, prioritizing and categorizing requests or content based on urgency and
importance. Its user interface simplifies complex processes, making Wikipedia maintenance more accessible and efficient
for users of all skill levels.

## Installation

Clone this extension into the `extensions/` directory in your local MediaWiki core repository.

Load the extension in LocalSettings.php:

```php
wfLoadExtension( 'Adiutor' );
```

Then visit Special:Adiutor settings to configure localization.


# License

GNU GENERAL PUBLIC LICENSE
Version 2, June 1991
