# Adiutor

https://www.mediawiki.org/wiki/Extension:Adiutor

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
Run the following command in the root directory of your MediaWiki installation:
```bash
cd maintenance
```
Run the following command to create the necessary configuration:
```bash
php run.php ../extensions/Adiutor/maintenance/updateConfiguration.php
```

Then visit Special:Adiutor settings to configure localization.


# License

This extension is licensed under the GNU General Public License version 3.0 or later (GPL-3.0-or-later).

http://www.gnu.org/copyleft/gpl.html
