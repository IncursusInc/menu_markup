# menu_markup
Module for Drupal 8 to allow HTML markup in menu titles.

By default, Drupal 8 will not accept HTML markup as part of a menu title. This module allows you to configure markup to be shown before and after specific menu titles (including submenu items).

This can be very handy if you want to show a glyphicon from Bootstrap or an icon from FontAwesome, etc. in front of your menu titles.

This version of the module only works with the v8.0.x releases of Drupal.

# Requirements
Drupal 8.1.x

# Standard usage scenario
1. Download and install the module.
2. Open admin/config/menu_markup/configure.
3. Enter in your desired values, with each menu item appearing on a separate line
4. Values should be in the format of: MENUTITLE|PREMARKUP|POSTMARKUP
5. You will need to clear (rebuild) your Drupal cache after saving your configuration (sadly) in order to see any changes you've made.

# Example configuration lines

```
Home|<span class="fa fa-home"></span>&nbsp;|
Some Menu Title|<span class="fa fa-home"></span>&nbsp;<strong>|</strong>
...
```

Credits / contact
-------------------------------------------------------------------
Currently maintained by Scott Burkett (incursus [1]).

Ongoing development is sponsored by Incursus, Inc. [2]

The best way to contact the authors is to submit an issue, be it a support request, a feature request or a bug report, in the project issue queue here in GitHub.

References
-------------------------------------------------------------------
1. https://www.drupal.org/u/incursus
2. http://incurs.us
