var emotool = {
  emoji_pattern : /\:fe[0-9a-f]{3}\:/gi,
  convert_to_emoji : function(text){
    var result = text;
    var matches = text.match(emotool.emoji_pattern);
    for(var i=0;matches && i<matches.length;i++){
      var match = matches[i];
      if (!emoji[match]) continue;
      var code = (emoji[match].code);
      result = result.replace(match, code);           
    }
    return result;
  },
  
  convert_to_emoji_for_preview : function(text){
    var result = text.replace(/[<]/g, '&lt;');
    result = result.replace(/[>]/g, "&gt;");
    result = result.replace(/\n/g, "<br />");
    var matches = result.match(emotool.emoji_pattern);
    for(var i=0;matches && i<matches.length;i++){
      var match = matches[i];
      if (!emoji[match]) continue;
      var src = emoji[match].img_url;
      result = result.replace(match, '<img src="'+src+'" width="15" height="15" />');           
    }
    return result;
  },
}