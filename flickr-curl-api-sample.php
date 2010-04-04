<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>Flickr API phot page example</title>
	<meta name="generator" content="BBEdit 8.7" />
	<style type="text/css" title="text/css">
/* A nice fancy css3 image gallery tutorial that inspired the css here, which is stripped down.

http://www.admixweb.com/2010/03/08/how-to-create-a-fancy-image-gallery-with-css3/
*/
#gallery {
border:10px solid #8F6F6F;
width:800px;
margin-left:auto;
margin-right:auto;
background:#3D2D27;
-webkit-box-shadow:#272229 10px 10px 20px;
-moz-box-shadow:#272229 10px 10px 20px;
filter:progid:DXImageTransform.Microsoft.Shadow(color='#272229', Direction=135, Strength=10);
box-shadow:#272229 10px 10px 20px;
}

ul#flickrPhotos {
width:100%;
margin:0;
padding:0;
}

ul#flickrPhotos li {
list-style-type:none;
display:inline-table;
width:33%;
position:relative;
padding:5px;
}

ul#flickrPhotos li img {
display:block;
border:1px solid #000;
margin:0 auto;
}
	</style>
</head>
<body>
<?php
require_once ('flickr-curl-api.php');
$flickrapi = new FlickrCurlAPI('');

$flickrapi->params = array ('media' => 'photos',
							'per_page' => 9,
							'privacy_filter' => 1);
$flickrapi->method = 'flickr.photos.search';
$flickrapi->methodInstance = 1;
$flickrapi->cache = true;
try {
	$flickrapi->api_flickr_call();
	$myImages = $flickrapi->preparePhotoThumbnails();
} catch(Exception $e) {
	echo 'Flickr API Error: ' . $e->getMessage(). "\n";
	}
$imgHTML = '<div id="gallery"><ul id="flickrPhotos">';
foreach($myImages as $flickrIMG){
	$imgHTML .= '<li>' . $flickrIMG .'</li>';
}
$imgHTML .= '</ul></div>';
echo $imgHTML;

?>
</body>
</html>
