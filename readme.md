# Git repository log viewer plugin for Dokuwiki

This plugin is based on: https://github.com/tuomasj/dokugitviewer

## Usage

Just add this into wiki page:

````
<gitlog:repository=my-repository-name [bare=0] [limit=10]>
````

And it will build you a list of your latest commits

## Requirements:

  * Bookmark-plugin installed for Dokuwiki
  * PHP5
  * Git

## Installation:

  1) Clone or download this into plugins/ directory in your dokuwiki installation
  2) activate plugin
  3) goto admin configuration page and set up plugin
  4) Add <gitlog> link into your wiki page


Author: Alexander Wenzel (alexander.wenzel.berlin@gmail.com)