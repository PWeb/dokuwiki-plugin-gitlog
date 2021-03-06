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

## Configuration

#### Location of your git executable

Usally ```/usr/bin/git``` on linux maschines.

If you use git on windows, use ```git```.

#### Root directory for your repositories

Put here the path, where your repositories are. This is optional!

You can customize the path to your repo also within the shortcode.

#### Date format

Use php ```date()``` format strings.

http://www.php.net/manual/en/function.date.php

## Usage

Just add this into wiki page:

````
<gitlog:repository="my-repository-name" [dir="custom/path/to/repo/"] [bare="0"] [limit="10"]>
````

Parameters in brackets [] are optional. Please dont use brackets within shortcode!


## Info

Author: Alexander Wenzel (https://plus.google.com/+alexwenzel86)

Plugin page: http://www.dokuwiki.org/plugin:gitlog