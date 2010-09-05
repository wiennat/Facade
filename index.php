<?php
/*
Facade : Add the emoji to your facebook status

Copyright (c) 2010, Wiennat and PrachP
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:
    * Redistributions of source code must retain the above copyright
      notice, this list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright
      notice, this list of conditions and the following disclaimer in the
      documentation and/or other materials provided with the distribution.
    * Neither the name of the Wiennat and PrachP nor the
      names of its contributors may be used to endorse or promote products
      derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL WIENNAT AND PRACHP BE LIABLE FOR ANY
DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

*/
require 'config.php';

// Create our Application instance.
$facebook = new Facebook(array(
  'appId'  => FACEBOOK_APP_ID,
  'secret' => FACEBOOK_APP_SECRET,
  'cookie' => true
));
$scopes = explode(',',$scope);
$session = $facebook->getSession();

$me = null;

$params = array (
                'fbconnect' => 0,
                'canvas'    => 1,
                'next'        => 'http://apps.facebook.com/facadedd/',
                'req_perms'    => $scope
            );
$loginUrl = $facebook->getLoginUrl($params);

// login or logout url will be needed depending on current user state.
$me = null;
$uid = $facebook->getUser();

?>
    <script src="<?php echo BASE_URL;?>static/new_emoji4.js"></script>
    <script src="<?php echo BASE_URL;?>static/emoji-tool3.js"></script>
    <style type="text/css">
		<?php echo file_get_contents("style.css") ?>
	</style>
	<fb:if-is-app-user>
  	<fb:else><fb:redirect url="<?php echo $loginUrl ?>" /></fb:else>
	</fb:if-is-app-user>
	<p id="permission_con">
	<fb:prompt-permission perms="publish_stream,user_photos,friends_photos,user_status,friends_status,user_videos,friends_videos" next_fbjs="show_main()"><h2> Click here to give us permissions to access your data in order to use Facade!</h2><p>You need to authorize Facade to access your data before using.</p></fb:prompt-permission>
	
	</p>
	<div id='facade_main' class="f_hidden" >
  	<fb:google-analytics uacct="UA-239852-8" />
    <h1>Hello <fb:name uid="loggedinuser" useyou="false" linked="false" /></h1>
    <fb:bookmark>Bookmark</fb:bookmark>
    <h4>How to use Facade</h4>
    <ul><li>1. Select where you want to post to. If you select "friend's wall", please select your friend by typing your friend's name in the textbook.</li>
<li>2. Type whatever you want and add whatever emoji you like</li>
<li>3. Copy black text in the red border section and paste in the text box below it.</li>
<li>4. Click Post button (if it take too long, please click it again)</li></ul>
    <p id="message"></p>
    <div id="mid">    
    <div id="update_pane">
    <form method="POST" id="facade_form">
    <div>Please select where you want to post to:</div>
	  <input type='radio' onclick="hide_friend_selector()" name='post_type' value="status" checked="checked">My status
	  <input type='radio' onclick="show_friend_selector()" name='post_type' value="friend">Friend's wall
	  <input type='radio' onclick="hide_friend_selector(); show_targeturl();" name='post_type' value="post" >Comment on post
      <p id="friend_selector_p" class="hide">Select your friend here: <fb:friend-selector idname="friend_id"></fb:friend-selector></p>
      <p id="targeturl" class="hide">
      Paste post's url here: <input type="text"  name="target_url"> You have to click on time of the post to go to single post page. Then, copy and paste it here.
      </p>     
      <div id="text_pane">
       
      <textarea name="status_temp" class="statusTextarea" id="status_temp" style="width:300px; margin-bottom:3px;"></textarea>
      <div style="border: solid 2px red;">
        <p style="color:red;">You must copy all text from here and paste in the textbox below.</p>
        <p id="actual_text" style="margin-left: 1em;"></p>
      </div>
      <p>What's on your mind?</p>
      <textarea name="status" class="statusTextarea" id="status" style="width:300px; margin-bottom:3px;"></textarea>
      <div id="preview_pane">
   		<h3>Preview:</h3>
   		<p id="preview_p"></p>
   		</div>
   	  </div>
      <a href="#" id="ajaxbtn" class="facade_btn">Post</a><span id="loading" class="loading f_hidden">posting….</span>
      
	</form>
   <fb:iframe src='<?php echo BASE_URL;?>ads.html' scrolling="no" frameborder="0" width="740" height="95"></fb:iframe>
</div>
    <div id="emoji"></div>
    <div id="footer" style="">
      Brought to you by wiennat and prachp. Made with the mixture of curiosity and oolong tea. 
    </div>
    </div>
    <fb:js-string var="goog" id="goog"></fb:js-string>
        <script>
        <!--   
	    var e = document.createElement('div');
	    var t = '',
	    	main_div = perm_con = document.getElementById('facade_main'),
	    	perm_con = document.getElementById('permission_con');
	    
	    if(!perm_con.getFirstChild()){
	    	main_div.setClassName('');
	    }

        for (var i in emoji){
       		var ei = document.createElement('img');
       		ei.setSrc(emoji[i].img_url);
       		ei.setStyle({width:'15px', height:'15px'});
       		ei.setValue(i)
       		ei.addEventListener('click', function(){
       			var d=document.getElementById('status_temp');
        		d.setValue(d.getValue() + this.getValue());
        		preview();
			});
       		e.appendChild(ei);
        }        
        document.getElementById('emoji').appendChild(e);
        function preview(e){        
        	var v = document.getElementById('status_temp').getValue();
        	var c = document.getElementById('preview_p'),
        	    e = document.getElementById('actual_text');
        	c.setTextValue(v);
        	e.setTextValue(emotool.convert_to_emoji(v));
        	//c.setInnerText(v + "xxxx");

        	c.setInnerXHTML("<span>" + emotool.convert_to_emoji_for_preview(v) + "</span>");
        }
        
        function toggleEmoji(e){
        	var e_div = document.getElementById('emoji');
        	e.preventDefault();
        	if (e_div.getClassName() === 'show'){
        		e_div.setClassName('f_hidden');
        		Animation(e_div).from('height','300px').to('height','0').go();
        	}else{
        		e_div.setClassName('show');
        		Animation(e_div).to('height','300px').from('height','0').go();
        	}
        }
        
        function sendRequest(e)
		{
			var ajax = new Ajax(),
			f = document.getElementById('facade_form'),
			ld = document.getElementById('loading'),
			params=f.serialize();
			ld.removeClassName('f_hidden');
  			ajax.responseType = Ajax.RAW;
  			
  			ajax.ondone = function(data){
  				var m = document.getElementById("message");
  				m.setTextValue(data);
  				ld.addClassName('f_hidden');
  				Animation(m).from('background-color','#ffff00').to('background-color','#ffffff').duration(1000).go();
  			}
  			
  			ajax.onerror = function(){
  				ld.addClassName('f_hidden');
  				var dialog = new Dialog(Dialog.DIALOG_CONTEXTUAL).setContext(ld).showMessage('Oops!','An error occur, Please try again.');
  			}  			
  			ajax.post("<?php echo BASE_URL;?>getemo.php",params)
  			
		}
		
		function hide_friend_selector(){
			document.getElementById('friend_selector_p').setClassName('f_hidden');
			document.getElementById('targeturl').setClassName('f_hidden');
		}
		
		function show_friend_selector(){
			document.getElementById('friend_selector_p').setClassName('show');
			document.getElementById('targeturl').setClassName('f_hidden');
		}
		
		function show_targeturl(){
			document.getElementById('targeturl').setClassName('show');
		}
		
		function show_main(){
			document.getElementById('facade_main').setClassName('');
			document.getElementById('permission_con').setClassName('f_hidden');	
		}
        
        document.getElementById('ajaxbtn').addEventListener('click', sendRequest);
        document.getElementById('status_temp').addEventListener('keyup', preview);
        //-->
    </script>
