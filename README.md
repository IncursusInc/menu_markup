# menu_markup v1.2
Module for Drupal 8 to allow HTML markup in menu titles.

By default, Drupal 8 will not accept HTML markup as part of a menu title. This module allows you to configure markup to be shown along with specific menu titles (including submenu items).

This can be very handy if you want to show a glyphicon from Bootstrap or an icon from FontAwesome, etc. in front of your menu titles.

This version of the module only works with the v8.0.x releases of Drupal.

# Requirements
Drupal 8.x

# Standard usage scenario
1. Download and install the module.
2. Open up the edit page for a menu link
3. Expand the Menu Markup section to see the options
4. Enter in your link markup
5. A special token @title can be used to substitute in the translated original title text
6. If you select a node type count, another special token called @nodeCount will be available representing the total count of published nodes of the selected type (useful for Bootstrap badges in menu items)

# Demo Site
You can see a demo of this in action on the Incursus web site (http://incurs.us)

# Example link markup

```
<span class="fa fa-home"></span>&nbsp;&nbsp;@title
```
```
<span class="fa fa-star"></span>&nbsp;&nbsp;<strong>@title</strong> <span class="badge">@nodeCount</span>
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
