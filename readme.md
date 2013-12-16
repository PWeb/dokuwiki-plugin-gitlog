# Git repository log viewer for Dokuwiki

This dokuwiki plugin displays your last commits for a given repository and the changed files.

This plugin is based on: https://github.com/tuomasj/dokugitviewer

## Demo

![Image](screenshot.png?raw=true)

## Requirements:

  * Git
  * PHP5

## Installation:

  - Clone or download this into plugins/ directory in your dokuwiki installation
  - if nessecary, rename directory to "gitlog"
  - activate plugin inside dokuwiki
  - goto admin configuration page, section "plugin/gitlog" and set up plugin
  - Add <gitlog> shortcode into your wiki page

## Usage

Just add this into wiki page:

````
<gitlog:repository=my-repository-name [bare=0] [limit=10]>
````

Parameters in brackets [] are optional. Please dont use brackets within shortcode!

And it will build you a list of your latest commits and the files changed.

## Info

Author: Alexander Wenzel (alexander.wenzel.berlin@gmail.com)

Plugin page: http://www.dokuwiki.org/plugin:gitlog