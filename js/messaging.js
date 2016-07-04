MESSAGING = function(){

    var $ = function(id){
	return document.getElementById(id);
    }

    var window = function(message, Y, id, plugin_url){
	return function(e){

	    if($(id+'-window')){
		$(id+'-window').parentNode.removeChild($(id+'-window'));
	    }

	    var win = $div({'_class': 'wpap-window', 'className': 'wpap-window', 'id':id+'-window', 'style':'position:fixed; z-index:99999;'});
	    document.body.appendChild(win);

	    win.innerHTML = message+' <img src="'+plugin_url+'ajaxspinner.gif"/>';

	    var left = ((Y.one('body').get('winWidth'))/2)-((Y.one(win).getStyle('width').replace('px', '')*1)/2)-20+'px';

	    var top = ((Y.one('body').get('winHeight'))/2)-((Y.one(win).getStyle('height').replace('px', '')*1)/1)+'px';
	    Y.one(win).setStyle('left', left);
	    Y.one(win).setStyle('top', top);

	}
    }
  
    var mask = function(message, Y, id){
	return function(e){
	    if(!$(id+'-mask')){
		var mask = $div({'_class': 'wpap-mask', 'className': 'wpap-mask', 'id':id+'-mask', 'style':'position:fixed; z-index:99998;width:'+Y.one('body').get('docWidth')+'px; height:'+Y.one('body').get('docHeight')+'px;top:0px;left:0px;'});
		Y.one(mask).setStyle('opacity', '0.7');
		document.body.appendChild(mask);
	    }
	}
    }

    return{

	show : function(Y, message, id, plugin_url){

	    if(!$(id+'-window')){
		mask(message, Y, id)(null);
	    }
	    window(message, Y, id, plugin_url)(null);

	},


	destroy : function(Y, id){

	    if($(id+'-window')){
		$(id+'-window').parentNode.removeChild($(id+'-window'));
		$(id+'-mask').parentNode.removeChild($(id+'-mask'));
	    }

	}


                   
    };

}();


