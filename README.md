wordpress-tumblr-post-from-csv-drag-and-drop
============================================

Shortcode that generates an HTML5 drag &amp; drop upload interface for CSV files of posts (of all types) to be posted on Tumblr. Handles connection to Tumblr as well. Plugin made on request.

Tumblr API Documentation:
http://www.tumblr.com/docs/en/api/v2

Register an Application on Tumblr:
http://www.tumblr.com/oauth/register

In the Application website just put the Wordpress URL.

Open Tumblr-CSV/tumblr/config.php and update Consumer Key and Secret and Wordpress URL.

Add [tumblrCSV] to a page or post. Even to a text widget (short codes have been enabled for them).

Example of accepted CSV format:

"text","tag1,tag2,tag3","27-05-2013","Post title","body"
"photo","tag1,tag2,tag3","27-05-2013","http://assets.tumblr.com/images/logo/logo.png?217d00ebeea3ee3907cb2a9d378fa325","test caption","http://google.ro"
"quote","tag1,tag2,tag3","27-05-2013","quote","source"
"link","tag1,tag2,tag3","27-05-2013","title","url","description"
"chat","tag1,tag2,tag3","27-05-2013","title","conversation"
"audio","tag1,tag2,tag3","27-05-2013","http://freedownloads.last.fm/download/569264057/Get%2BGot.mp3","caption"
"video","tag1,tag2,tag3","27-05-2013",'<iframe width="560" height="315" src="//www.youtube.com/embed/b6dD-I7kJmM" frameborder="0" allowfullscreen></iframe>',"caption"


To do:

More security checks, reorganize code, comment code.