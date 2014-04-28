<?php
/*
Plugin Name: Tumbler CSV - Connect, Upload CSV and Posts to Queue - Shortcode
Plugin URI: http://www.nenuadrian.com
Description: A simple complex plugin that implements a smart shortcode which allows users to connect to Tumblr from that page/widget and drag and drop a CSV file in a specific format which will add posts to their Tubmlr post queue.
Version: 1.0
Author: Nenu Adrian
Author URI: http://www.nenuadrian.com/
License: GPL2
*/

require(ABSPATH . WPINC . '/pluggable.php');

$version = 1;
class TumblrCSV
{
  
  function __construct()
  {
    
    add_shortcode('tumblrCSV', array(
      $this,
      'tumblrCSV'
    ));
    
    // make sure text widget process's short codes
    add_filter('widget_text', 'do_shortcode' );

    
    add_action('wp_ajax_my_action', array(
      $this,
      'upload_callback'
    ));
    add_action('wp_ajax_nopriv_my_action', array(
      $this,
      'upload_callback'
    ));
    

    add_action('init', array(
      $this,
      'myStartSession'
    ), 1);
    add_action('wp_logout', array(
      $this,
      'myEndSession'
    ));
    add_action('wp_login', array(
      $this,
      'myEndSession'
    ));
  }
  
  function current_page_url()
  {
    $pageURL = 'http';
    if (isset($_SERVER["HTTPS"])) {
      if ($_SERVER["HTTPS"] == "on") {
        $pageURL .= "s";
      } //$_SERVER["HTTPS"] == "on"
    } //isset($_SERVER["HTTPS"])
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
      $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
    } //$_SERVER["SERVER_PORT"] != "80"
    else {
      $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    }
    return $pageURL;
  }
  
  
  
  function myStartSession()
  {
    if (!session_id()) {
      session_start();
    } //!session_id()
  }
  
  function myEndSession()
  {
    session_destroy();
  }
  /*
   * Does the magic
   */
  function tumblrCSV($atts = array(), $contents = "")
  {
    if (isset($_REQUEST["logout"]))
        unset($_SESSION["access_token"]);
        
    require("tumblr/index.php");
    
    
    
      
    if ($content->meta->status == 401) {
      
      $url = get_permalink();
      $url = $url ? $url : $this->current_page_url();
      $url = plugins_url('tumblr/redirect.php', __FILE__) . "?redirect=" . urlencode($url);
      echo '<a href="' . $url . '">Connect with Tumblr</a>';
    } //$content->meta->status == 401
    elseif ($content->meta->status == 200) {
    
      
        
      if (isset($_FILES["fileselect"])) {
        $this->returnMessage = true;
        $message             = $this->upload_callback(false);
      } //isset($_FILES["fileselect"])
      
      $username = $content->response->user->name;
      $message  = 'Hey, ' . $username . '. Drop CSV file here.';
      
      
      $return = '
    
        <link rel="stylesheet" type="text/css" href="' . plugins_url('css/styles.css', __FILE__) . '">

        <!--[if lt IE 9]>
        <script src="' . plugins_url('js/html5shiv.js', __FILE__) . '"></script>
        <![endif]-->
        <form id="upload" action="#" method="POST" enctype="multipart/form-data">
          '.wp_nonce_field('upload_tumblr_csv','token', null, false).'
          <div>
            <input type="file" id="fileselect" name="fileselect" >
            <div id="filedrag">' . $message . '</div>
          </div>

          <div id="submitbutton">
            <button type="submit">Upload Files</button>
          </div>


        </form>
        <form method="post"><input type="submit" name="logout" value="Logout"/></form>
       
      ';
      $return .= '<script>var ajaxurl = "' . admin_url('admin-ajax.php') . '";</script>';
      $return .= '<script src="' . plugins_url('js/csv.js', __FILE__) . '"></script>';
      
    } //$content->meta->status == 200
    return $return;
  }
  
  function upload_callback()
  {

    $message = false;

    if ( 
      ! isset( $_POST['token'] ) 
      || ! wp_verify_nonce( $_POST['token'], 'upload_tumblr_csv' ) 
    ) 
    {
      $message = "Invalid security token";
    }
    else
    {
      require("tumblr/index.php");
      
      if ($content->meta->status != 200) {
        $message = "Not authenticated with tumblr";
      } //$content->meta->status != 200
      else if (isset($_FILES["fileselect"])) {
        $file = $_FILES["fileselect"];
        if ($file["size"] <= 1024000) {
          if ($file["type"] == "text/csv") {
            $lines = explode("\n", file_get_contents($file["tmp_name"]));
            
            foreach ($user_info->response->user->blogs as $blog) {
              if ($blog->primary === true) {
                break;
              } //$blog->primary === true
            } //$user_info->response->user->blogs as $blog
            $hostname = parse_url($blog->url, PHP_URL_HOST);
            
            foreach ($lines as $line) {
              $line          = str_getcsv($line);
              $post          = array();
              $post["type"]  = $line[0];
              $post["tags"]  = $line[1];
              $post["date"]  = $line[2];
              $post["state"] = "queue";
              switch ($line[0]) {
                case "video":
                  $post["embed"]   = $line[3];
                  $post["caption"] = $line[4];
                  
                  break;
                case "chat":
                  $post["title"]        = $line[3];
                  $post["conversation"] = $line[4];
                  break;
                case "audio":
                  $post["data"]         = $line[3];
                  $post["external_url"] = $line[3];
                  $post["caption"]      = $line[4];
                  break;
                case "photo":
                  $post["source"]  = $line[3];
                  $post["data"]    = (file_get_contents($line[3]));
                  $post["caption"] = $line[4];
                  $post["link"]    = $line[5];
                  break;
                case "quote":
                  $post["quote"]  = $line[3];
                  $post["source"] = $line[4];
                  break;
                case "link":
                  $post["title"]       = $line[3];
                  $post["url"]         = $line[4];
                  $post["description"] = $line[5];
                  break;
                default:
                  $post["title"] = "text";
                  $post["title"] = $line[3];
                  $post["body"]  = $line[4];
              } //$line[0]
              
              $connection->post("blog/$hostname/post", $post);
              
            } //$lines as $line
            
            $message = count($lines) . " posts sent";
            
          } //$file["type"] == "text/csv"
          else
            $message = "Invalid file type. Must be CSV";
        } //$file["size"] <= 1024000
        else
          $message = "File too big. Max 1 MB.";
      } //isset($_FILES["fileselect"])
      else
        $message = "No file supplied";
    }

    if (isset($this->returnMessage))
      return $message;
    
    echo json_encode(array(
      "message" => $message
    ));
    die(); // this is required to return a proper result
  }
  
}

$TumblrCSV = new TumblrCSV();

?>