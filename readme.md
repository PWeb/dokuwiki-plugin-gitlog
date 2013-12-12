# Git repository log viewer plugin for Dokuwiki
----

This plugin is based on: https://github.com/tuomasj/dokugitviewer

The main idea is this:

````
git commit -m "Fixed a typo bug #bug24"
````

After committing, the wiki plugin will show commit messages and
convert "#bug24" into internal link to bug-page with correct anchor.

It also workd this way too:

````
git commit -m "Started working for login page (#ft4)"
````

The "#ft4" is transformed into internal link to features page and creating
hyperlink into #ft4 (you need bookmark-plugin for creating those anchors)

Just add this into wiki page:

````
<gitlog:repository=my-repository-name features=backlog bugs=bugdb limit=15>
````

And it will build you a list of your latest commits

# Requirements:

  * Bookmark-plugin installed for Dokuwiki
  * PHP5
  * Git

# Installation:

  1) Clone this repository into plugins/ directory in your dokuwiki directory
  2) activate plugin
  3) goto admin configuration page and set up plugin
  4) Add <gitlog> link into your wiki page

Dokuwiki: http://www.dokuwiki.org

Author: Alexander Wenzel (alexander.wenzel.berlin@gmail.com)