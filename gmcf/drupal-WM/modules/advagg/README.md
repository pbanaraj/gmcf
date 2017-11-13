
----------------------------------
ADVANCED CSS/JS AGGREGATION MODULE
----------------------------------


CONTENTS OF THIS FILE
---------------------

 - Features & benefits
 - Configuration
 - JSMin PHP Extension
 - JavaScript Bookmarklet
 - How to get a high PageSpeed score


FEATURES & BENEFITS
-------------------

**Advanced CSS/JS Aggregation core features**

 - AdvAgg for Drupal 8 has some significant differences from Drupal 7; instead
   of totally reworking asset handling, AdvAgg only applies some improvements.
   This is mostly because the core handling is much better. It is also better
   for compatibility with quite a few other modules such as http2_server_push.
 - Sub modules can provide significant optimization of resources.
 - Url query string to turn off AdvAgg for that request. ?advagg=0 will
   turn off file optimization if the user has the "bypass advanced aggregation"
   permission. ?advagg=-1 will completely bypass all of Advanced CSS/JS
   Aggregations modules and submodules. ?advagg=1 will enable Advanced CSS/JS
   Aggregation if it is currently disabled.
 - Button on the admin page for dropping a cookie that will turn off file
   optimization. Useful for theme development.
 - Gzip support. All optimized files can be pre-compressed into a .gz file and
   served from Apache. This is faster then gzipping the file on each request.

**Included submodules**

 - advagg_cdn:
   Load CSS or JavaScript libraries from a public CDN; currently only supports
   jQuery and jQuery UI with either Google's or Microsoft's CDN.
 - advagg_css_minify:
   Minify the  CSS files using a 3rd party minifier; currently supports YUI
   (included) or the Core minification algorithm.
 - advagg_js_minify:
   Compress the compiled JavaScript files using a 3rd party minifier;
   built in support for a number of minifiers.
 - advagg_mod:
   Includes additional tweaks that may not work for all sites:
   - Force preprocessing for all CSS/JS.
   - Add defer tag to all JS.
   - Defer CSS loading using `rel=preload` and JavaScript Polyfill.
   - Add async tag to all or only local JavaScript.
 - advagg_ext_minify:
   Minify CSS or JS through an otherwise unsupported method, with a
   configurable command line.


CONFIGURATION
-------------

Settings page is located at:
`admin/config/development/performance/advagg`

**Global Options**

 - Enable Advanced Aggregation: Check this to start using this module. You can
   also quickly disable the module here. For testing purposes, this has the same
   effect as placing ?advagg=-1 in the URL. Disabled by default.
 - Create .gz files: Check this by default as it will improve your performance.
   For every Aggregated file generated, this will create a gzip version of file
   and then only serve it out if the browser accepts gzip files compression.
   Enabled by default.
 - Use Cores Grouping Logic: Leave this checkbox enabled until you are ready to
   begin exploring the AdvAgg Bundler sub-module which overrides Core's
   functionality. This groups files just like Core does so should just work.
   Enabled by default. You will also have to disable this checkbox if you wish
   to enable some of the CSS Options below on this settings page.
 - AdvAgg Cache Settings: As a reference, core takes about 25 ms to run.
   Development will scan all files for a change on every page load. Normal is
   fine for all use cases. Aggressive should be fine in almost all use cases;
   if your inline css/js changes based off of a variable then the aggressive
   cache hit ratio will be low; if that is the case consider using
   Drupal.settings for a better cache hit ratio.

**CSS Options & JS Options**

 - Combine CSS files by using media queries: "Use cores grouping logic" needs to
   be unchecked in order for this to work. Also noted is that due to an issue
   with IE9, compatibility mode is forced off if this is enabled by adding this
   tag in the html head:

       <!--[if IE]>
       <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
       <![endif]-->

   Disabled by default.
 - Fix improperly set type (CSS/JS): If type is external but does not start with
   http, https, or // change it to be type file. If type is file but it starts
   with http, https, or // change type to be external.

**Information page**

located at `admin/config/development/performance/advagg/info`. This page
provides debugging information. There are no configuration options here.
 - Hook Theme Info: Displays the process_html order. Used for debugging.
 - Core Asset Hooks Implemented by Modules: List modules implementing the
   various core asset hook functions.
 - Get information about an optimized file.

**Operations page**

located at `admin/config/development/performance/advagg/operations`. This is a
collection of commands to control the cache and to manage testing of this
module. In general this page is useful when troubleshooting some aggregation
issues.

 - Aggregation Bypass Cookie
    - Toggle The "aggregation bypass cookie" For This Browser: This will set or
      remove a cookie that disables aggregation for the remainder of the browser
      session. It acts almost the same as adding ?advagg=0 to every URL.

 - Cron Maintenance Tasks
   - Remove All Stale Files: Scan all files in the advagg_css/js directories and
     remove the ones that have not been accessed in the last 30 days.

 - Drastic Measures
   - Clear All Caches: Remove all data stored in the advagg cache bins, delete
     all files.
   - Increment Global Counter: Force the creation of all new file with new names
     incrementing a global counter.

JSMIN PHP EXTENSION
-------------------

The AdvAgg JS Minify module can take advantage of jsmin.c. JavaScript parsing
and minimizing will be done in C instead of PHP dramatically speeding up the
process. JsMin C extension can be found at https://github.com/sqmk/pecl-jsmin.


JAVASCRIPT BOOKMARKLET
----------------------

You can use this JS code as a bookmarklet for toggling the AdvAgg URL parameter.
See http://en.wikipedia.org/wiki/Bookmarklet for more details.

    javascript:(function(){
      var loc = document.location.href,
          qs = document.location.search,
          regex_off = /\&?advagg=-1/,
          goto = loc;
      if(qs.match(regex_off)) {
        goto = loc.replace(regex_off, '');
      } else {
        qs = qs ? qs + '&advagg=-1' : '?advagg=-1';
        goto = document.location.pathname + qs;
      }
      window.location = goto;
    })();

HOW TO GET A HIGH PAGESPEED SCORE
---------------------------------

Go to `admin/config/development/performance/advagg`
 - uncheck "Use cores grouping logic"
 - check "Combine CSS files by using media queries"

Install AdvAgg Modifier if not enabled and go to
`admin/config/development/performance/advagg/mod`
 - Under "Move JS to the footer" Select "All"
 - set "Enable preprocess on all JS/CSS"
 - set "Move JavaScript added by drupal_add_html_head() into drupal_add_js()"
 - set "Move CSS added by drupal_add_html_head() into drupal_add_css()"
 - Enable every checkbox under "Optimize JavaScript/CSS Ordering"

Install AdvAgg Minify Javascript if not enabled and go to
`admin/config/development/performance/advagg/js-minify`
 - Select JSMin if available; otherwise select JSMin+
