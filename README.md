# Plugin: Company List Dashboard Widget by Leuchtfeuer



## Overview

This plugin brings a new Dashobard Widget to Mautic which 

It is part of the "ABM" suite of plugins that extends Mautic capabilities for working with Companies.

## Requirements
- Mautic 5.x (minimum 5.1)
- PHP 8.1 or higher
- Company Tags and Company Segments Plugins

## Installation
### Composer
This plugin can be installed through composer.

### Manual install
Alternatively, it can be installed manually, following the usual steps:

* Download the plugin
* Unzip to the Mautic `plugins` directory
* Rename folder to `LeuchtfeuerCompanySegmentMembersWidgetBundle` 

-
* In the Mautic backend, go to the `Plugins` page as an administrator
* Click on the `Install/Upgrade Plugins` button to install the Plugin.

OR

* If you have shell access, execute `php bin\console mautic:plugins:reload` to install the plugins.

## Plugin Activation and Configuration
1. Go to `Plugins` page
2. Click on the plugin with the icon currently named `Widget Comp Segments`
3. ENABLE the plugin

## Usage
In the Dashboard, click the "New" button.

In the "Add widget" dialog, seledct the new Type "Company Segment Members" within the new Section "Company Widgets".

You can now choose no, one, or multiple Company Segments to filter for (empty selection means "show all").

The sorting and the number of lines can currently not be configured.

You can have multiple instances of this Widget in parallel, which is useful for having different filters.

Please note that the output of this Widget is covered by the **Mautic cache,** which is set to **10 minutes** lifetime by default. This value can be changed in the Mautic system configuration ("Cached data timeout", `cached_data_timeout`).

## Troubleshooting
Make sure you have not only installed but also enabled the Plugin.

If things are still funny, please try

`php bin/console cache:clear`

and 

`php bin/console mautic:assets:generate`

## Known Issues
* Column witdh not dynamic

## Future Ideas
* Rename Plugin (currently reference to "Segments" is too narrow)
* Filtering for Tags
* Configurable columns
* Actions from within Widget
* Honor "Company" permissions (as soon as that exists in Mautic :)

## Credits
* @JonasLudwig1998
* @lenonleite
* @ekkeguembel
* @PatrickJenkner
* @pzarleuchtfeuer

## Author and Contact
Leuchtfeuer Digital Marketing GmbH

Please raise any issues in GitHub.

For all other things, please email mautic-plugins@Leuchtfeuer.com