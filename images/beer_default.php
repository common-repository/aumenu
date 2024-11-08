<?php
	
	header("Content-type: image/svg+xml");
	print('<?xml version="1.0" encoding="utf-8"?>');
	
	$color = '#' . (isset($_GET['c']) ? $_GET['c'] : 'ffffff');
?>
<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 viewBox="0 0 37.1 66.5" enable-background="new 0 0 37.1 66.5" xml:space="preserve">
<path fill="<?= $color ?>" d="M36.3,21l0.8-9.8L35.1,11l-0.6,7.6c-3.2-3.2-9.1-5.3-16-5.3c-6.9,0-12.9,2.2-16.1,5.4L1.9,11L0,11.2l0.7,10
	c0,0.6-0.1,1-0.2,1.5c-0.1,0.6-0.3,1.2-0.2,2.1c0.1,0.7,0.3,1.3,0.4,1.7c0.2,0.5,0.3,0.9,0.4,1.7l2.3,29.4c0,5,6.7,9,15.2,9
	s15.2-4,15.2-8.9l2.2-29.7c0.1-0.8,0.2-1.3,0.4-1.8c0.2-0.5,0.4-1,0.4-1.8c0.1-0.9-0.1-1.5-0.2-2C36.3,21.9,36.2,21.5,36.3,21z
	 M34.5,25.5c0,0,0,0.1,0,0.1l0-0.6c0,0-1.1-2-3,1.6c-1.9,3.6-4.7,5.1-5.6,5.5c-0.9,0.4-8.4-1.2-14.4-3c-5.9-1.8-4.3-0.5-8.3-5.2
	l3.2,1l-1.9,0.5c-0.1-1-1.6,0.9-1.9,0.3c-0.2-0.4-0.3-0.8-0.3-1.2c0-0.5,0-0.9,0.2-1.5c0.1-0.2,0.1-0.5,0.2-0.8
	c1.6-4,8.2-6.7,16-6.7v-0.1c7.7,0,14.2,2.8,15.9,6.7c0,0.2,0.1,0.5,0.2,0.7c0.1,0.5,0.2,0.8,0.2,1.4C34.8,24.7,34.6,25.1,34.5,25.5z
	"/>
<ellipse fill="#FFFFFF" cx="18.5" cy="23.9" rx="16.4" ry="8.7"/>
<ellipse fill="<?= $color ?>" cx="18.5" cy="24" rx="5.8" ry="3.3"/>
<polygon fill="#FFFFFF" points="18.4,39.8 33.1,27.9 4.2,28.1 "/>
<circle fill="#FFFFFF" cx="26.5" cy="41.9" r="2.7"/>
<circle fill="#FFFFFF" cx="21.8" cy="45.5" r="0.9"/>
<circle fill="#FFFFFF" cx="25.7" cy="49.9" r="1.4"/>
<circle fill="#FFFFFF" cx="27.1" cy="55.8" r="0.5"/>
<path fill="<?= $color ?>" d="M18.5,22C8.1,22,0,17.2,0,11S8.1,0,18.5,0c10.4,0,18.5,4.8,18.5,11c0,3.9-3.3,7.4-8.8,9.4
	c-0.6,0.2-1.3-0.1-1.5-0.7c-0.2-0.6,0.1-1.3,0.7-1.5c4.5-1.6,7.2-4.4,7.2-7.2c0-4.7-7.4-8.7-16.2-8.7S2.3,6.3,2.3,11
	c0,4.7,7.4,8.7,16.2,8.7c2.1,0,4.2-0.2,6.2-0.7c0.6-0.1,1.2,0.2,1.4,0.9s-0.2,1.2-0.9,1.4C23.1,21.8,20.9,22,18.5,22z"/>
</svg>
