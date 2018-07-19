# wpText2Speech
Plugin Wordpress to set text2speech utilities

Documentation
=============

<img src="https://nouslesdevs.com/wp-content/uploads/2018/02/t2s.gif" />

How it work
-----------
This plugin makes it possible to convert text into mp3 soundtrack through an external Watson service.

Via a precise HTML markup, a click on a button launches an Ajax request on a PHP page that will launch a request on the Watson server (or other). Once recovering the system records the mp3 and sends the data to the Javascript code that will play the mp3.

The system is able to detect files that have already been sent so as not to launch queries for already built texts.

UPDATE: I have now added the management of French, English, Portuguese and German.

Required
--------
Wordpress and jQuery

How to use
----------
1) Add the plugin in your Wordpress
2) Go to extension and activate them
3) In sidebar you can see new menu "wpT2S Options" go that
4) Configure that (next point)

Configuration
-------------
Option in Wordpress

- API Point (endpoint of service) : The endpoint of Watson service
Example :  https://watson-api-explorer.ng.bluemix.net/text-to-speech/api/v1/synthesize

- Content class selector : The class of the block that must be "played"
Example : col-d-play (bootstrap's humor)

- Icon base : The default icon that will be attached to the title

- Icon loading : The icon waiting for the result

- Icon play : The icon for playing the mp3

- Icon pause : The icon for pause mp3

Specific markup
---------------
You need to take the option "class selector" configure in the Wordpress plugin and put it as class in the wrapper of what you want to read and simply add a title (h1, h2 or other). The plugin does the rest.

```html
<div class="t2s_section">
   <h2>Songoku<span class="icon"></span></h2>
</div>
```