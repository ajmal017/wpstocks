$(document).ready(function(){  

    console.log("Document ready");
    console.log(document.location.href);

    if('applicationCache' in window) {
	var appCache = window.applicationCache;
	appCache.addEventListener('updateready', function() {
            appCache.swapCache();
            if(confirm('App update is available. Update now?')) {
		window.location.reload();
            }
	}, false);
    }

    $('#ds_add_domain_button').on('click', addDomain());

    $('#kw_fetch_synonyms_button').on('click', fetchSynonyms($('#kw_keyword')) );
    $('#kw_check_match_button').on('click', checkMatch($('#kw_keyword'), $('#kw_keyword2')) );
    $('#pagerank_button').on('click', getSEO('pagerank', $('#kw_url'), null));
    $('#pagespeed_button').on('click', getSEO('pagespeed', $('#kw_url'), null));
    $('#mozrank_button').on('click', getSEO('mozrank', $('#kw_url'), null));
    $('#mozrankraw_button').on('click', getSEO('mozrankraw', $('#kw_url'), null));
    $('#linkcount_button').on('click', getSEO('linkcount', $('#kw_url'), null));
    $('#equitylinkcount_button').on('click', getSEO('equitylinkcount', $('#kw_url'), null));
    $('#pageauthority_button').on('click', getSEO('pageauthority', $('#kw_url'), null));
    $('#domainauthority_button').on('click', getSEO('domainauthority', $('#kw_url'), null));
    $('#googleplusshares_button').on('click', getSEO('googleplusshares', $('#kw_url'), null));
    $('#twittershares_button').on('click', getSEO('twittershares', $('#kw_url'), null));
    $('#facebookshares_button').on('click', getSEO('facebookshares', $('#kw_url'), null));
    $('#pinterestshares_button').on('click', getSEO('pinterestshares', $('#kw_url'), null));
    $('#linkedinshares_button').on('click', getSEO('linkedinshares', $('#kw_url'), null));
    $('#deliciousshares_button').on('click', getSEO('deliciousshares', $('#kw_url'), null));
    $('#diggshares_button').on('click', getSEO('diggshares', $('#kw_url'), null));
    $('#stumbleuponshares_button').on('click', getSEO('stumbleuponshares', $('#kw_url'), null));
    $('#serps_button').on('click', getSEO('serps', null, $('#kw_keyword')));

});

var addDomain = function(){
    return function(e){
	if(e){
	    e.preventDefault(); //STOP default action
	}
	var domains = $('#ds_domain_name').val().split(",");
	var i = null;
	var domain = null;
	console.log(domains);
	for(i in domains){
	    domain = domains[i];
	    console.log("Adding domain "+domain);
	    if(domain==''){
		alert('Please enter a domain name');
	    }
	    else{
		$('#ds_domain_list').append($li({}, domain));
		var container = $div({});
		$(container).append($h2({}, domain));
		var backlinks_list_container = $ul({'style':'list-style:none; width:1000px; margin:0;padding:0;'});
		$(container).append(backlinks_list_container);
		$('#ds_results_container').append(container);
		var callback = renderBacklinks(backlinks_list_container);
		getSEO('backlinks', domain, null, callback)(null);
	    }
	}
    }
}

var renderBacklinks = function(backlinks_list_container){
    return function(jqXHR){
	/*
	  {"url_to":"http://cnn.com/","url_from":"http://101.0.107.83/bbs/space-uid-396393.html","ahrefs_rank":7,"domain_rating":42,"ip_from":"101.0.107.83","links_internal":50,"links_external":6,"page_size":19057,"encoding":"gbk","title":"innoriape的空间 - 南澳中文信息网 阿德莱德华人论坛 - Powered by Discuz!","first_seen":"2014-11-18T07:28:57Z","last_visited":"2014-11-18T07:28:57Z","prev_visited":"","original":true,"redirect":0,"alt":"","anchor":"http://CNN.COM","text_pre":"个人主页","text_post":"","sitewide":false,"link_type":"href","nofollow":false}
	*/
	console.log('renderBacklinks');
	var backlinks = jqXHR.responseJSON.refpages;
	var i = null;
//	var domain = $('#ds_domain_name').val();
	var PR_container = null;
	var PA_container = null;
	for(i in backlinks){
	    domain = backlinks[i].url_from;
	    PR_container = $li({'style':'float:left; margin-right: 10px;'});
	    PA_container = $li({'style':'float:left; margin-right: 10px'});
	    $(backlinks_list_container).append(
		$li({'style':'float:left;width:100%;'}, $ul({'style':'margin:0;padding:0;list-style:none;float:left; width: 100%;'},
							    $li({'style':'white-space:nowrap;float:left; margin-right:10px'}, backlinks[i].url_from),
							    PR_container,
							    PA_container,
							    $li({'style':'float:left;'}, '#Links: '+(backlinks[i].links_internal + backlinks[i].links_external)+""))));
	    domain = domain.replace("http://", "");
	    getSEO('pagerank', domain, null, renderPageRank(PR_container))(null);	    
	    getSEO('pageauthority', domain, null, renderPageAuthority(PA_container))(null);	    
	}
    }
}

var renderPageRank = function(container){
    return function(jqXHR){
	var pr = jqXHR.responseJSON.pagerank;
	if(pr=='Failed to generate a valid hash for PR check.'){
	    pr='?';
	}
	container.innerText = 'PR'+pr;
    }
}

var renderPageAuthority = function(container){
    return function(jqXHR){
	var pa = jqXHR.responseJSON.pageauthority;
	container.innerText = 'PA'+pa;
    }
}

var renderSEO = function(method){
    return function(jqXHR){
	if(method=='facebookshares' || method == 'pagespeed'){
	    alert(jqXHR.responseText);
	}
	else if(method=="serps"){
	    alert(jqXHR.responseText);		    
	}
	else{
	    alert(jqXHR.responseJSON[method]);
	}
    }
}

var getSEO = function(method, domain, keyword_obj, callback){
    return function(e){
	$.ajax('services/seo/?method='+method, {
	    'type':'GET',
	    'error':(function(jqXHR, testStatus, errorThrown){
		console.log('Error fetching '+method);
		console.log('Domain:'+domain);
		console.log(jqXHR);
		console.log(errorThrown);
	    }),
	    'success':(function(data, textStatus, jqXHR){
		console.log('Success fetching '+method);
		console.log(jqXHR);
		console.log(method);
		callback(jqXHR);
	    }),
	    'statusCode':{
		404:function(){
		}
	    },
	    'accepts':'application/json',
	    'data':'url='+(domain==null?'':domain)+'&keyword='+(keyword_obj==null?'':$(keyword_obj).val()),
	    'dataType':'json',
	});
    }
}

var fetchSynonyms = function(keywordObj){
    return function(e){
	if(e){
	    e.preventDefault(); //STOP default action
	}
	$.ajax('services/synonyms/?word='+$(keywordObj).val(), {
	    'type':'GET',
	    'error':(function(jqXHR, testStatus, errorThrown){
		console.log('Error getting synonyms');
		console.log(jqXHR);
		console.log(errorThrown);
	    }),
	    'success':(function(data, textStatus, jqXHR){
		console.log('Success getting synonyms');
		$('#kw_error_log').html('');
		console.log(jqXHR);
		alert(jqXHR.responseJSON);
	    }),
	    'statusCode':{
		404:function(){
		    console.log('No synonyms found in database');
		    $.ajax('services/synonyms/', {
			'type':'POST',
			'error':(function(jqXHR, testStatus, errorThrown){
			    console.log('Error fetching synonyms');
			    console.log(jqXHR);
//			    $('#kw_error_log').html(jqXHR.responseText + errorThrown);
			    console.log(jqXHR.responseText);
			    console.log(errorThrown);
			}),
			'success':(function(data, textStatus, jqXHR){
			    $('#kw_error_log').html('');
			    console.log('Success fetching synonyms');
			    console.log(jqXHR);
			    alert(jqXHR.responseJSON);
			}),
			'statusCode':{
			    404:function(){
				console.log('No synonyms found');
				alert("No synonyms found");
			    },
			    400:function(){
				console.log('Error getting synonyms (400)');
			    },
			    405:function(){
				console.log('Mysql error');
			    }
			},
			'accepts':'application/json',
			'data':'word='+$(keywordObj).val(),
			'dataType':'json',
		    });
		},
		400:function(){
		    console.log('Error getting synonyms (400)');
		},
		405:function(){
		    console.log('Mysql error');
		}
	    },
	    'accepts':'application/json',
	    'dataType':'json',
	});
    }
}


var checkMatch = function(keywordObj, keyword2Obj){
    return function(e){
	if(e){
	    e.preventDefault(); //STOP default action
	}
	$.ajax('services/synonyms/?word='+$(keywordObj).val()+'&word2='+$(keyword2Obj).val(), {
	    'type':'GET',
	    'error':(function(jqXHR, testStatus, errorThrown){
		console.log('Error getting match detail');
		console.log(jqXHR);
		console.log(errorThrown);
	    }),
	    'success':(function(data, textStatus, jqXHR){
		console.log('Success getting match detail');
		$('#kw_error_log').html('');
		console.log(jqXHR);
		alert(jqXHR.responseText);
	    }),
	    'statusCode':{
		404:function(){
		},
		400:function(){
		    console.log('Error getting match detail (400)');
		},
		405:function(){
		    console.log('Mysql error');
		}
	    },
	    'accepts':'application/json',
	    'dataType':'json',
	});
    }
}


