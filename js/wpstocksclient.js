jQuery( document ).ready( function (){  

    console.log( "Client ready" );

    $( '#wpstocks_registrationForm' ).on( 'submit', register () );

	// Initialize the add account link (+) but only if client is admin.
	getClientDetails( getClientCallback() );

	var cb = renderAccountsTable( $( '#accountsTableBody' ), true );
	getAccounts( cb )( null );

	console.log( jQuery( '#clientDetailsContainer' ).length );
	if ( jQuery( '#clientDetailsContainer' ).length > 0 ) {
		var callback = renderClientDetailsTable( jQuery( '#clientDetailsContainer' ) );
		getClientDetails( callback );
	}

	if ( $( '#wpstocksPortfolioBody' ).length > 0 ) {
		var portfolioCallback = renderPortfolioTable( $( '#wpstocksPortfolioBody' ) );
		getPortfolio( portfolioCallback );
	}


	getWatchList( getWatchlistCallback( $( '#wpstocksWatchlistBody' ) ) )( null );

	// Company logo
	// .upload_logo is a link or button
	$( '.wpstocks_upload_logo' ).click( function( e ) {
		e.preventDefault();
		// hat tip: http://stackoverflow.com/questions/13847714/wordpress-3-5-custom-media-upload-for-your-theme-options
		var logo_uploader = wp.media( {
			title: 'Select a logo image',
			button: {
				text: 'Upload'
			},
			multiple: false
		} )
			.on( 'select', function() {
				var attachment = logo_uploader.state().get( 'selection' ).first().toJSON();
				$( '.wpstocks_logomedia_image' ).attr( 'src', attachment.url );
				$( '.wpstocks_logo_media_url' ).val( attachment.url );
				$( '.wpstocks_logo_media_id' ).val( attachment.id );
			} )
			.open();
	} );
	
    console.log( "Finished init client" );

} );

var getClientCallback = function() {

	return function( client ) {
		console.log(client);
		if ( !client['admin' ]) {

			$('.addAccount').on( 'click', function( e ) {
				alert('You must be administrator to add accounts.');
			} );

			$( '#wpstocksPortfolioBuyButton' ).on( 'click', function( e ) {
				alert('You must be administrator to buy stock.');
			} );

			$( '#wpstocksWatchlistAddButton' ).on( 'click', function( e ) {
				alert('You must be administrator to add watches.');
			} );

		}
		else {

			$('.addAccount').fancybox({
				'content': '<h4>Add an account</h4><div class="container" style="width:500px"><div class="row"><div class="col-sm-12" style="padding-left: 0px !important;"><input class="form-control input-lg" type="text" value="" required="yes" name="wpstocks_accountName" id="wpstocks_accountName" placeholder="Enter a name for the account" /></div><div class="col-sm-12"><button style="margin-top: 5px;" type="submit" class="btn" id="createAccountButton">Create</button></div></div></div>',
				'width': '400px',
				'afterShow': function () {
					$('#createAccountButton').on('click', createAccount(this, $('#accountsTableBody')))
				}
			});

			// Buy button
			$( '#wpstocksPortfolioBuyButton' ).fancybox( {
				'content':'<h4>Open New Trading Position</h4><div class="container" style="width:500px"><div class="row"><div class="col-sm-12" style="padding-left:0px"><label>Ticker Code</label></div><div class="col-sm-12" style="padding-left:0px"><input class="form-control input-lg" type="text" value="" required="yes" name="wpstocks_tickerCode" id="wpstocks_tickerCode" placeholder="" /></div><div class="col-sm-12" style="padding-left:0px"><button style="margin-top: 5px; margin-right: 5px;" type="submit" class="btn" id="wpstocks_getQuotesButton">Get Quotes</button><button style="margin-top: 5px;" type="button" class="btn" id="wpstocks_cancelGetQuotesButton">Cancel</button></div></div></div>',
				'width':'400px',
				'afterShow': function (){
					$( '#wpstocks_getQuotesButton' ).on( 'click', getQuotes () );
					$( '#wpstocks_cancelGetQuotes' ).on( 'click', function( e ){
						$.fancybox.close ();
					} );
				}
			} );

			// Watchlist button
			$( '#wpstocksWatchlistAddButton' ).fancybox( {
				'content':'<h4>Add to Watch List</h4><div class="container" style="width:500px"><div class="row"><div class="col-sm-12" style="padding-left:0px"><label>Ticker Code</label></div><div class="col-sm-12" style="padding-left:0px"><input class="form-control input-lg" type="text" value="" required="yes" name="wpstocks_tickerCode" id="wpstocks_tickerCode" placeholder="" /></div><div class="col-sm-12" style="padding-left:0px"><button style="margin-top: 5px;margin-right:5px;" type="submit" class="btn" id="wpstocks_addWatchButton">Add</button><button style="margin-top: 5px; margin-right:5px" type="button" class="btn" id="wpstocks_cancelAddWatchButton">Cancel</button></div></div></div>',
				'width':'400px',
				'afterShow': function (){
					$( '#wpstocks_addWatchButton' ).on( 'click', addItemToWatchlist( addItemToWatchlistCallback( $( '#wpstocksWatchlistBody' ) ) ) );
					$( '#wpstocks_cancelAddWatchButton' ).on( 'click', function( e ){
						$.fancybox.close ();
					} );
				}
			} );
		}
	}
};

var renderPortfolioTable = function( container ) {
	return function( data ){
		console.log( 'renderPortfolioTable ()' );
		var i = null;
		var records = data[ 'records' ];
		for ( i in records ) {
			addToPortfolioTable( records[i], records[i]['accountName'] );
		}
	}
};

var getPortfolio = function( callback ) {
    console.log( 'getPortfolio ()' );
    console.log( wpstocks_js_parameters.ajax_url+'?action=wpstocks_ajax_getPortfolio' );
    $.ajax( wpstocks_js_parameters.ajax_url+'?action=wpstocks_ajax_getPortfolio', {  // For POSTS must include trailing /
	'type':'GET',
	'error':( function( jqXHR, testStatus, errorThrown ){
	    console.log( 'Error' );
	    console.log( jqXHR );
	    console.log( errorThrown );
	} ),
	'success':( function( data, textStatus, jqXHR ){
	    console.log( 'Successfully got portfolio data' );
	    console.log( jqXHR );
	    callback( jqXHR.responseJSON );
	} ),
	'statusCode':{
	    404:function (){
	    },
	    403:function (){
		alert( 'You have been logged out. Please login again' );
	    }
	},
	'accepts':'application/json',
	'dataType':'json'
    } );
}

var getQuotes = function ()
{
	return function( e ){
		$.fancybox.close ();
		$( '#portfolioPanel' ).css( 'display','none' );
		$( '#getQuotesPanel' ).css( 'display','block' );
		var callback = renderEquityTables( $( '#wpstocksBuyEquityTableBody' ), $( '#wpstocksStockQuotesTableBody' ) );
		getStock( $( '#wpstocks_tickerCode' ).val (), callback );
	}
}

var getQuote = function( stock )
{
    return function( e ){
	$.fancybox.close ();
	$( '#portfolioPanel' ).css( 'display','none' );
	$( '#getQuotesPanel' ).css( 'display','block' );
	$( '#watchlistPanel' ).css( 'display','none' );
	var callback = renderEquityTables( $( '#wpstocksBuyEquityTableBody' ), $( '#wpstocksStockQuotesTableBody' ) );
	getStock( stock, callback );
    }
}

var addWatch = function ()
{
    return function( e ){
	$.fancybox.close ();
	$( '#portfolioPanel' ).css( 'display','none' );
	$( '#watchlistPanel' ).css( 'display','none' );
	$( '#getQuotesPanel' ).css( 'display','block' );

//	var callback = renderBuyEquityTableBody( $( '#wpstocksBuyEquityTableBody' ), $( '#wpstocksStockQuotesTableBody' ) );
	var callback = renderEquityTables( $( '#wpstocksBuyEquityTableBody' ), $( '#wpstocksStockQuotesTableBody' ) );
	getStock( $( '#wpstocks_tickerCode' ).val (), callback );


//	getAccountDetailsCallback = renderAccountDetails( jqXHR.responseJSON[accountName]['accountNumber'], accountName );
//	$( detailLink ).on( 'click', getAccountDetails( accountName, jqXHR.responseJSON[accountName]['accountNumber'], getAccountDetailsCallback ) );

    }
}

var renderEquityTables = function( wpstocksBuyEquityTableBody, wpstocksStockQuotesTableBody )
{
    return function( stockData ){
	$( wpstocksBuyEquityTableBody ).html( '' );
	$( wpstocksStockQuotesTableBody ).html( '' );
	renderBuyEquityTableBody( wpstocksBuyEquityTableBody )( stockData )
	renderStockQuotesTableBody( wpstocksStockQuotesTableBody )( stockData )
    }
}

var renderStockQuotesTableBody = function( wpstocksStockQuotesTableBody )
{
	return function( stockData ){

		$( wpstocksStockQuotesTableBody ).append( $tr( {},
			$td( { 'colspan':'2'}, $h4( {},stockData[0]['name']+'' ),"( ",stockData[0]['symbol']+''," )" ) ) );


		$( wpstocksStockQuotesTableBody ).append(
			$tr( {},
				$td( {},'Last trade:' ),
				$td( { 'style':'white-space: nowrap;', "_class" : "currency", "className" : "currency" },stockData[0]['lastPrice']+' ' +stockData[0]['currency']+'' ) ) );

		$( wpstocksStockQuotesTableBody ).append(
			$tr( $td( {},'Day\'s range' ),
				$td( { "_class" : "currency", "className" : "currency"},stockData[0]['low']+' ' +stockData[0]['currency']+'', ' - ', stockData[0]['high']+'' ) ) );

		$( wpstocksStockQuotesTableBody ).append(
			$tr( {},
				$td( {},'Trade time:' ),
				$td( { 'style':'white-space: nowrap;',},stockData[0]['date']+'' ) ) );

		$( wpstocksStockQuotesTableBody ).append(
			$tr( $td( { "_class" : "number", "className" : "number" },'Volume' ),
				$td( { 'style':'white-space: nowrap;', "_class" : "number", "className" : "number" },stockData[0]['volume']+'' ) ) );

		$( wpstocksStockQuotesTableBody ).append(
			$tr( {},
				$td( {},'Change:' ),
				$td( { 'style':'white-space: nowrap;', "_class" : "number", "className" : "number" }, stockData[0]['netChange']+'', '( ', stockData[0]['percentChange']+'', ' )' ) ) );

		$( wpstocksStockQuotesTableBody ).append(
			$tr( {},
				$td( {},'Open:' ),
				$td( { 'style':'white-space: nowrap;', "_class" : "currency", "className" : "currency" }, stockData[0]['open']+' '+stockData[0]['currency']+'' +'') ) );

		$( wpstocksStockQuotesTableBody ).append(
			$tr( $td( { "_class" : "currency", "className" : "currency" },'Close' ),
				$td( { 'style':'white-space: nowrap;', "_class" : "currency", "className" : "currency" }, stockData[0]['close']+' '+stockData[0]['currency']+'' ) ) );

	}
}

var getStock = function( stock, callback )
{
    console.log( 'Calling getStock ()' );
	if ( stock !='' && stock != 'null' ) {
		$.ajax( wpstocks_js_parameters.plugin_url + 'api/', {
			'type': 'GET',
			'error': ( function ( jqXHR, testStatus, errorThrown ) {
				console.log( 'Error' );
				console.log( jqXHR );
				console.log( errorThrown );
			} ),
			'success': ( function ( data, textStatus, jqXHR ) {
				console.log( 'Success' );
				console.log( jqXHR );
				callback( jqXHR.responseJSON );
			} ),
			'statusCode': {
				404: function  () {
				},
				400: function  () {
					alert( 'Error getting stock information: missing parameter' );
				}
			},
			'accepts': 'application/json',
			'data': 'stock=' + stock + '&api=barchart&apikey=489911a5880b02d7093e260e0158fd39',
			'dataType': 'json'
		} );
	}
}

var renderAccountSelectionTable = function( container ) {

	return function( jqXHR ){
		console.log( "renderAccountSelectionTable ()" );
		console.log( jqXHR );
		var table = $div( {'_class':'container-fluid', 'className':'container-fluid'} );
		$( container ).append( table )
		var radio = null;
		var accountName = null;
		var accountNameInput = $input( {'id':'accountNameInput', 'type':'hidden'} );
		var accountNumberInput = $input( {'id':'accountNumberInput', 'type':'hidden'} );
		var accountAmountInput = $input( {'id':'accountAmountInput', 'type':'hidden'} );
		$( container ).append( accountNameInput );
		$( container ).append( accountNumberInput );
		$( container ).append( accountAmountInput );
		var accounts = jqXHR.responseJSON['accounts'];
		for ( accountName in accounts ) {
			if ( accountName !='' ) {
				console.log( accountName );
				radio = $input( {'type':'radio','value':accountName,'name':'selectedAccountRadio','group':'selectedAccount','_class':'selectedAccount', 'className':'selectedAccount'} );
				$( radio ).on( 'click', setAccountNameInput( accountName, accounts[accountName]['accountNumber']+'', accounts[accountName]['balance']+'' ) );
				$( table ).append( $div( { '_class':'row', 'className':'row'},
					$div( { 'style':'white-space: nowrap; padding-left:0px;', '_class':'col-sm-6', 'className':'col-sm-6' }, radio, ' ', accountName + '' ),
				//	$div( { '_class':'col-sm-4', 'className':'col-sm-4' }, accounts[accountName]['accountNumber']+'' ),
					$div( { "_class" : "currency col-sm-6", "className" : "col-sm-6 currency" }, accounts[accountName]['balance']+' '+accounts[accountName]['currency']+'' ) ) );
			}
		}
	}
}

var setAccountNameInput = function( accountName, accountNumber, accountAmount )
{
    return function( e ){
	$( accountNameInput ).val( accountName );
	$( accountNumberInput ).val( accountNumber );
	$( accountAmountInput ).val( accountAmount );
    }
}

var renderBuyEquityTableBody = function( container ) {
	return function( stockData ){

		console.log( "renderBuyEquityTableBody ()" );
		$( '#getQuotesPanel' ).css( 'display','block' );
		$( '#portfolioPanel' ).css( 'display','none' );
		$( '#watchlistPanel' ).css( 'display','none' );
		/*
		 [{"symbol":"IBM","name":"International Business Machines","lastPrice":138.6,"date":"2015-11-25 00:40:00","netChange":0.14,"percentChange":0.1,"open":137.65,"high":139.34,"low":137.31,"close":138.6,"volume":3407500}]
		 */
//	$( container ).html( 'Last price:'+stockData[0]['lastPrice'] );

		var accountSelectionTableContainer = $div( {'id':'accountSelectionTableContainer'} );
		var cb = renderAccountSelectionTable( accountSelectionTableContainer );
		getAccounts( cb )( null );

		var totalField = $input( {'type':'text', 'disabled':true, 'id':'totalField', 'name':'totalField', 'value':stockData[0]['lastPrice']+''} );
		var quantityField = $input( {'type':'number','value':'1'} );

		$( quantityField ).on( 'keyup',function( e ){
			var newTotal = $( quantityField ).val ()*1 * ( stockData[0]['lastPrice']*1 );
			$( totalField ).val( newTotal+'' );
		} );

		$( quantityField ).on( 'change',function( e ){
			var newTotal = $( quantityField ).val ()*1 * ( stockData[0]['lastPrice']*1 );
			$( totalField ).val( newTotal+'' );
		} );

		var openPositionButton = $button( {}, 'Open Position' );
		var cancelButton = $button( {}, 'Cancel' );

		$( container ).append( $tr( {},
			$td( {},
				$label( {}, 'Account' ) ),
			$td( {}, accountSelectionTableContainer ) ) );


		$( container ).append( $tr( {},
			$td( {},
				$label( {}, 'Stock' ) ),
			$td( {}, stockData[0]['name']+'' ) ) );

		$( container ).append( $tr( {},
			$td( {},
				$label( {}, 'Price' ) ),
			$td( { "_class" : "currency", "className" : "currency" }, stockData[0]['lastPrice']+' '+stockData[0]['currency']+'' ) ) );

		$( container ).append( $tr( {},
			$td( {},
				$label( {}, 'Quantity' ) ),
			$td( { "_class" : "number", "className" : "number" }, quantityField ) ) );

		$( container ).append( $tr( {},
			$td( {},
				$label( {}, 'Total' ) ),
			$td( { "_class" : "currency center-block", "className" : "currency center-block" }, totalField, ' ',stockData[0]['currency']+'' ) ) );

		$( container ).append( $tr( {},
			$td( {},
				$label( {}, '' ) ),
			$td( {}, openPositionButton, ' ', cancelButton ) ) );

		$( openPositionButton ).on( 'click', function( e ){

			if ( $( accountNameInput ).val () == '' ) {
				alert( 'Please select an account' );
			}
			else if ( ( $( accountAmountInput ).val () * 1 ) < ( $( totalField ).val () * 1 ) ) {
				alert( 'You do not have enough money in the selected account' );
			}
			else{

				$.ajax( wpstocks_js_parameters.ajax_url+'?action=wpstocks_ajax_openPosition', {  // For POSTS must include trailing /
					'type':'POST',
					'error':( function( jqXHR, testStatus, errorThrown ){
						console.log( 'Error' );
						console.log( jqXHR );
						console.log( errorThrown );
					} ),
					'success':( function( data, textStatus, jqXHR ){

						console.log( 'Successfully opened position' );
						console.log( jqXHR );

						addToPortfolioTable( jqXHR.responseJSON, $( accountNameInput ).val () );

						// Update accounts tables
						updateTables( $( accountNameInput ).val (), $( accountNumberInput ).val () );

						$( '#getQuotesPanel' ).css( 'display','none' );
						$( '#portfolioPanel' ).css( 'display','block' );
						$( '#watchlistPanel' ).css( 'display','block' );


					} ),
					'statusCode':{
						404:function (){
						}
					},
					'accepts':'application/json',
					'data': 'amount='+$( totalField ).val ()+'&quantity='+$( quantityField ).val ()+'&price='+stockData[0]['lastPrice']+'&stock='+stockData[0]['symbol']+'&accountName='+$( accountNameInput ).val ()+'&accountNumber='+$( accountNumberInput ).val (),
					'dataType':'json'
				} );

			}
		} );

	}
}

var updateTables = function( accountName, accountNumber )
{
    console.log( 'Updating tables' );
    $( '#accountsTableBody' ).html( '' );
    getAccounts( renderAccountsTable( $( '#accountsTableBody' ), false ) )( null );
    $( '#accountSelectionTableContainer' ).html( '' );
    getAccounts( renderAccountSelectionTable( $( '#accountSelectionTableContainer' ) ) )( null );
    getAccountDetails( accountName, accountNumber, renderAccountDetails( accountNumber, accountName, false ) )( null );
}

var addToPortfolioTable = function( data, account ) {
	
	console.log( 'addToPortfolioTable ()' );
	console.log( data );
	console.log( account );

    var currentPriceCell = $td( {'_class':'currentPrice currency', "className" : "currentPrice, currency"},data['stock_price']+' '+data['currency']+'' );
    var profitCell = $td( { "className": "profit currency", "_class":"profit currency"}, '0' );
    var newRow = $tr( {},
		    $td( {},account + '' ),
		    $td( {},data['stock_symbol']+'' ),
		    $td( { "_class" : "number", "className" : "number" },data['stock_quantity']+'' ),
		    $td( { "_class" : "currency", "className" : "currency" }, data['stock_price'] + ' ' + data['currency']+'' ),
		    $td( { "_class" : "currency", "className" : "currency" }, ( data['stock_price'] * data['stock_quantity'] )+ ' ' + data['currency'] + '' ),
		    currentPriceCell,
		    profitCell,
		    $td( {},data['name']+'' ) );

    var callback = updatePortfolioRow( data['stock_symbol'] + '', data['stock_quantity'] + '', data['stock_price'] + '', currentPriceCell, profitCell, data['currency'] );
    getStock( data['stock_symbol']+'', callback );

    $( '#wpstocksPortfolioBody' ).append( newRow );

}

var updatePortfolioRow = function ( symbol, quantity, oldPrice, currentPriceCell, profitCell, currency ) {
	return function(  stockData  ){

		console.log(  'updatePortfolioRow  ()'  );
		console.log(  'Got stock data for '+symbol  );
		console.log(  stockData  );
		/*
		 [{"symbol":"IBM","name":"International Business Machines","lastPrice":138.6,"date":"2015-11-25 00:40:00","netChange":0.14,"percentChange":0.1,"open":137.65,"high":139.34,"low":137.31,"close":138.6,"volume":3407500}]
		 */
		$(  currentPriceCell  ).html(  stockData[0]['lastPrice'] + ' ' + currency + '' );
		oldPrice = oldPrice.replace(  "$", ""  );
		var oldAmount = (  oldPrice*1  ) * (  quantity*1  );
		var newAmount = (  (  stockData[0]['lastPrice']  ).replace(  "$",""  )*1  ) * (  quantity*1  );
		var profit = newAmount * 1 - oldAmount * 1;
		$(  profitCell  ).html( profit.toFixed( 2 ) + ' ' + currency + '' );
	}
}

var renderClientDetailsTable = function( container )
{
    return function( clientDetails ){
	console.log( "Rendering client details" );
	console.log( clientDetails );
	var clientDetailsTable = $table( {"_class":"table", "className":"table"},
                $tbody( {},
                   $tr( {},
                      $td( { 'style':'border-top:0px;'}, "Name:" ),
                      $td( { 'style':'border-top:0px;' }, clientDetails['name'] + '' )
                   ),
                   $tr( {},
                      $td( {}, "Company:" ),
                      $td( {}, clientDetails['company'] + '' )
                   ),
                   $tr( {},
                      $td( { 'colspan':'2' }, "Address:" )
                   ),
					$tr( {},
						$td( { 'colspan':'2' , 'style':'border:0px' }, clientDetails['address1'] + '' )
					),
					$tr( {},
						$td( { 'colspan':'2', 'style':'border:0px' }, clientDetails['address2'] + '' )
					),
					$tr( {},
						$td( { 'colspan':'2', 'style':'border:0px' }, clientDetails['address3'] + '' )
					),
					$tr( {},
						$td( { 'colspan':'2', 'style':'border:0px' }, clientDetails['address4'] + '' )
					),
					$tr( {},
						$td( {}, "City:" ),
						$td( {}, clientDetails['city'] + '' )
					),
					$tr( {},
						$td( {}, "Country:" ),
						$td( {}, clientDetails['country'] + '' )
					),
                   $tr( {},
                      $td( {}, "Fax:" ),
                      $td( {}, clientDetails['fax'] + '' )
                   ),
					$tr( {},
						$td( {}, "Email:" ),
						$td( {}, clientDetails['email'] + '' )
					),
					$tr( {},
						$td( {}, "Second Email:" ),
						$td( {}, clientDetails['secondaryemail']  + '')
					),
					$tr( {},
						$td( {}, "Office Phone:" ),
						$td( {}, clientDetails['officephone'] + '' )
					),
					$tr( {},
						$td( {}, "Home Phone:" ),
						$td( {}, clientDetails['homephone'] + '' )
					),
					$tr( {},
						$td( {}, "Mobile:" ),
						$td( {}, clientDetails['mobile'] + '' )
					)
				)
	);
	jQuery( container ).html( "" );
	jQuery( container ).append( clientDetailsTable );
    }
}

var getClientDetails = function( callback )
{
    jQuery.ajax( wpstocks_js_parameters.ajax_url+'?action=wpstocks_ajax_getClientDetails', {  // For POSTS must include trailing /
	'type':'GET',
	'error':( function( jqXHR, testStatus, errorThrown ){
	    console.log( 'Error' );
	    console.log( jqXHR );
	    console.log( errorThrown );
	} ),
	'success':( function( data, textStatus, jqXHR ){
	    console.log( 'Successfully got client details' );
	    console.log( jqXHR );
	    console.log( callback );
	    callback( jqXHR.responseJSON );
	} ),
	'statusCode':{
	    404:function (){
	    }
	},
	'accepts':'application/json',
	'dataType':'json'
    } );
}

var handleStatementButtonClick = function( accountNumber, accountName, accountDetails ) 
{
    return function( e ){
	$( '#accountDetailsContainer' ).html( "" );
	$( '#accountDetailsContainer' ).append( $( getAccountDetailsTable( accountName, accountNumber, accountDetails[ 'transactions'], accountDetails['balance'] ) ) );
    }
}

var getTransactionsByAccount = function( accountName, callback )
{
	console.log('Getting transactions');
	console.log( wpstocks_js_parameters.ajax_url+'?action=wpstocks_ajax_getTransactions&accountName='+accountName );
	jQuery.ajax( wpstocks_js_parameters.ajax_url+'?action=wpstocks_ajax_getTransactions&accountName='+accountName, {
		'type':'GET',
		'error':( function( jqXHR, testStatus, errorThrown ){
			console.log( 'Error' );
			console.log( jqXHR );
			console.log( errorThrown );
		} ),
		'success':( function( data, textStatus, jqXHR ){
			console.log( 'Successfully got transactions' );
			console.log( jqXHR );
			console.log( callback );
			callback( jqXHR.responseJSON );
		} ),
		'statusCode':{
			404:function (){
				$ ( '#accountDetailsContainer' ).html( 'No transactions found' );
			}
		},
		'accepts':'application/json',
		'dataType':'json'
	} );
}

var renderAccountDetails = function ( accountNumber, accountName, activateTab ) 
{
	return function ( accountDetails ) {

		console.log ( 'Calling renderAccountDetails  () ' ) ;
		console.log ( 'Account details:' ) ;
		console.log ( accountDetails ) ;
		if  ( activateTab )  {
			activateAccountsTab  () ;
		}
		var renderTransactionsCallback = function  ()  {
			return function  ( accountDetails )  {
				console.log( 'renderTransactionsCallback ()' );
				console.log( accountDetails );
				$ ( '#accountDetailsContainer' ) .html ( "" ) ;
				$ ( '#accountDetailsContainer' ) .append ( $( getAccountDetailsTable( accountName, accountNumber, accountDetails['transactions'], accountDetails['balance'] )  )  ) ;
			}
		}

		getTransactionsByAccount ( accountName, renderTransactionsCallback () ) ;

	}
}

var activateAccountsTab = function ()
{
    console.log( 'Calling activateAccountsTab ()' );
    jQuery( '.tab-pane' ).removeClass( 'active' );
    jQuery( '#tab2' ).addClass( 'active' );
    jQuery( '.nav-tabs' ).find( "li" ).removeClass( 'active' );
    jQuery( '#accountsTab' ).addClass( 'active' );
}

var handleAccountDetailsClick = function( accountNumber, accountName )
{
    return function( e ){

	console.log( 'Calling handleAccountDetailsClick ()' );
	console.log( 'Account name='+accountName );
	console.log( 'Account number='+accountNumber );
	if ( e !=null ) {
	    e.preventDefault (); //STOP default action
	    activateAccountsTab ();
	}
    }
}

var renderAccountsTable = function( accountsTableBody, loadAccountDetailsTable ){
	return function( jqXHR ){

		var accountName = null;
		var accountAmount = null;
		var row = null;
		var detailLink = null;
		var getAccountDetailsCallback = null

		console.log( 'Rendering accounts table' );
		console.log( jqXHR.responseJSON );

		var i = 0;
		var accounts = jqXHR.responseJSON['accounts'];

		for ( accountName in accounts ) {

			if ( accountName != '' && ( accounts[accountName]['accountNumber']+'' ) != '' ) {
				detailLink = $a( {'href':'#'}, accountName +'' );
				getAccountDetailsCallback = renderAccountDetails( accounts[accountName]['accountNumber'], accountName, true );
				$( detailLink ).on( 'click', getAccountDetails( accountName, accounts[accountName]['accountNumber'], getAccountDetailsCallback ) );

				if ( i == 0 && loadAccountDetailsTable ) {
					getAccountDetails( accountName, accounts[accountName]['accountNumber'], renderAccountDetails( accounts[accountName]['accountNumber'], accountName, false ) )( null )
					i++;
				}

				//row = $tr( $td( {}, detailLink ), $td( {}, accountName +''), $td( {}, accounts[accountName]['currency'] +''), $td( { "_class" : "currency", "className" : "currency" }, (accounts[accountName]['balance'] + ' '+accounts[accountName]['currency']+'')+'' ) );
				row = $tr( $td( {}, detailLink ), $td( {}, accounts[accountName]['currency'] +''), $td( { "_class" : "currency", "className" : "currency" }, (accounts[accountName]['balance'] + ' '+accounts[accountName]['currency']+'')+'' ) );
				$( accountsTableBody ).append( row );
			}
		}
	}
}

var getAccounts = function( callback )
{
    return function( e ){ 

	console.log( 'Calling getAccounts ()' );
	console.log( wpstocks_js_parameters.ajax_url+'?action=wpstocks_ajax_getAccounts' );
	$.ajax( wpstocks_js_parameters.ajax_url+'?action=wpstocks_ajax_getAccounts', {  // For POSTS must include trailing /
	    'type':'GET',
	    'error':( function( jqXHR, testStatus, errorThrown ){
		console.log( 'Error' );
		console.log( jqXHR );
		console.log( errorThrown );
	    } ),
	    'success':( function( data, textStatus, jqXHR ){
		console.log( 'Successfully got accounts' );
		console.log( jqXHR );
		callback( jqXHR );
	    } ),
	    'statusCode':{
		404:function (){
		},
		403:function (){
		    alert( 'You have been logged out. Please login again' );
		}
	    },
	    'accepts':'application/json',
	    'dataType':'json',
	} );
    }
}

var getAccountDetails = function( accountName, accountNumber, callback )
{
    return function( e ){

	console.log( 'Calling getAccountDetails ()' );

	$.ajax( wpstocks_js_parameters.ajax_url+'?action=wpstocks_ajax_getAccountDetails', {  // For POSTS must include trailing /
	    'type':'GET',
	    'error':( function( jqXHR, testStatus, errorThrown ){
		console.log( 'Error' );
		console.log( jqXHR );
		console.log( errorThrown );
	    } ),
	    'success':( function( data, textStatus, jqXHR ){
		console.log( 'Success' );
		console.log( jqXHR );
		callback( jqXHR.responseJSON );

		// Cache the results so we don't have to call the server again
		if ( e!=null ) {
		    $( e.target ).detach( 'click' );
		    $( e.target ).on( 'click', function( e ){
			callback( jqXHR.responseJSON );
		    } );
		}

	    } ),
	    'statusCode':{
		404:function (){
		}
	    },
	    'accepts':'application/json',
	    'dataType':'json',
	    'data': 'accountNumber='+encodeURIComponent( accountNumber )+'&accountName='+encodeURIComponent( accountName )
	} );

    }
}

var getAccountNavigation = function( accountName, accountNumber, activeButton, accountDetails )
{
    console.log( 'Calling getAccountNavigation ()' );
    var statementButton = $a( {"href":"#"},"Statement" );
    var depositButton = $a( {"href":"#"},"Deposit" );
    var withdrawButton = $a( {"href":"#"},"Withdraw" );

    $( statementButton ).on( 'click', handleStatementButtonClick( accountNumber, accountName, accountDetails ) );
    $( depositButton ).on( 'click', renderDepositForm( accountName, accountNumber, $( '#accountDetailsContainer' ), accountDetails ) );
    $( withdrawButton ).on( 'click', renderWithdrawForm( accountName, accountNumber, $( '#accountDetailsContainer' ), accountDetails ) );



    var navigation = $nav( {"_class":"pull-right", "className":"pull-right"},
			  $ul( {"_class":"nav nav-pills", "className":"nav nav-pills"},
			      $li( {"_class":activeButton=='statementButton'?"active":"", "className":activeButton=='statementButton'?"active":""},
				  statementButton ),
			      $li( {"_class":activeButton=='depositButton'?"active":"", "className":activeButton=='depositButton'?"active":""},
				  depositButton ),
			      $li( {"_class":activeButton=='withdrawButton'?"active":"", "className":activeButton=='withdrawButton'?"active":""},
				  withdrawButton ) ) );
    return navigation;
}

var renderDepositForm = function( accountName, accountNumber, container, accountDetails )
{
    return function( e ){
	console.log( 'Calling renderDepositForm ()' );
	$( container ).html( '' );
	$( container ).append( getDepositForm( accountName, accountNumber, accountDetails ) );
    }
}

var renderWithdrawForm = function( accountName, accountNumber, container, accountDetails )
{
    return function( e ){
	console.log( 'Calling renderWithdrawForm ()' );
	$( container ).html( '' );
	$( container ).append( getWithdrawForm( accountName, accountNumber, accountDetails ) );
    }
}

var submitWithdrawForm = function( accountName, accountNumber, balance )
{
    return function( e ){
	e.preventDefault (); //STOP default action
	console.log( 'Calling submitWithdrawForm ()' );
	if ( ( balance*1 ) > ( $( '#withdrawAmount' ).val ()*1 ) && ( $( '#withdrawAmount' ).val ()*1 ) <= 1000 ) {
	    makeWithdraw( accountName, accountNumber );
	}
	else if ( ( $( '#withdrawAmount' ).val ()*1 ) > 1000 ) {
	    alert( "You cannot withdraw more than 1000" );
	}
	else{
	    alert( "You do not have enough money to make this withdraw" );
	}
    }
}

var submitDepositForm = function( accountName, accountNumber )
{
    return function( e ){
	e.preventDefault (); //STOP default action
	console.log( 'Calling submitDepositForm ()' );
	makeDeposit( accountName, accountNumber );
    }
}

var makeDeposit = function( accountName, accountNumber )
{
    console.log( 'Calling makeDeposit ()' );
    $.ajax( wpstocks_js_parameters.ajax_url+'?action=wpstocks_ajax_makeDeposit', {  // For POSTS must include trailing /
	'type':'POST',
	'error':( function( jqXHR, testStatus, errorThrown ){
	    console.log( 'Error' );
	    console.log( jqXHR );
	    console.log( errorThrown );
	} ),
	'success':( function( data, textStatus, jqXHR ){

	    console.log( 'Success' );
	    console.log( jqXHR );

	    getAccountDetailsCallback = renderAccountDetails( accountNumber, accountName, true );
	    getAccountDetails( accountName, accountNumber, getAccountDetailsCallback )( null );

	    updateTables( accountName, accountNumber );
		
	} ),
	'statusCode':{
	    404:function (){
	    },
	    403:function (){
		alert( 'You have been logged out. Please login again.' );
	    },
	    400:function (){
		alert( 'Error: Missing parameter.' );
	    }
	},
	'data':'accountName='+accountName+'&accountNumber='+accountNumber+'&amount='+$( '#depositAmount' ).val ()+'&ttnumber='+$( '#depositTTnumber' ).val ()
    } );
}

var makeWithdraw = function( accountName, accountNumber )
{
    console.log( 'Calling makeWithdraw ()' );
    $.ajax( wpstocks_js_parameters.ajax_url+'?action=wpstocks_ajax_makeWithdraw', {  // For POSTS must include trailing /
	'type':'POST',
	'error':( function( jqXHR, testStatus, errorThrown ){
	    console.log( 'Error' );
	    console.log( jqXHR );
	    console.log( errorThrown );
	} ),
	'success':( function( data, textStatus, jqXHR ){

	    console.log( 'Successfully made deposit' );
	    console.log( jqXHR );
	    getAccountDetailsCallback = renderAccountDetails( accountNumber, accountName, true );
	    getAccountDetails( accountName, accountNumber, getAccountDetailsCallback )( null );

	    updateTables( accountName, accountNumber );

	} ),
	'statusCode':{
	    404:function (){
	    },
	    403:function (){
		alert( 'You have been logged out. Please login again.' );
	    },
	    400:function (){
		alert( 'Error: Missing parameter.' );
	    }
	},
	'data':'accountName='+accountName+'&accountNumber='+accountNumber+'&amount='+$( '#withdrawAmount' ).val ()
    } );
}

var getDepositForm = function( accountName, accountNumber, accountDetails )
{
    console.log( 'Calling getDepositForm ()' );

    var depositButton = $button( {'type':'submit', '_class':'btn btn-primary', '_className':'btn btn-primary'}, 'Submit' );
    var cancelButton = $button( {'type':'button', '_class':'btn btn-default', '_className':'btn btn-default'}, 'Cancel' );

	$( cancelButton ).on( 'click', handleStatementButtonClick( accountNumber, accountName, accountDetails ) );

	var depositFormCallback = function( depositButton ) {
		return function ( client ) {
			if ( !client['admin'] ) {
				$( depositButton ).on( 'click', function( e ) {
					e.preventDefault();
					alert( 'You must be administrator to make a deposit.' );
				} );
			}
			else {
				$( depositButton ).on( 'click', submitDepositForm( accountName, accountNumber ) );
			}
		}
	}

	getClientDetails( depositFormCallback( depositButton ) );

    var form = $form( {'id':'wpstocksDepositForm'} );
    $( form ).append( $div( {"_class":"row", "className":"row"},
	 $div( {"_class":"col-sm-4", "className":"col-sm-4"},
              $label( {'_class':'control-label',  'for':''}, 'Amount' ) ),
	 $div( {"_class":"col-sm-8", "className":"col-sm-8"},
              $div( {'_class':'controls', 'className':'controls'},
                   $input( {'_class':'form-control', 'className':'form-control input-lg','placeholder':'', 'type':'text', 'id':'depositAmount', 'name':'amount'} ) ) ) ) );

    $( form ).append( $div( {"_class":"row", "className":"row"},
	 $div( {"_class":"col-sm-4", "className":"col-sm-4"},
              $label( {'_class':'control-label',  'for':''}, 'Reference Number' ) ),
	 $div( {"_class":"col-sm-8", "className":"col-sm-8"},
              $div( {'_class':'controls', 'className':'controls'},
                   $input( {'_class':'form-control', 'className':'form-control input-lg','placeholder':'', 'id':'depositTTnumber', 'type':'text', 'name':'ttnumber'} ) ) ) ) );


    $( form ).append( $div( {"_class":"row", "className":"row"},
	 $div( {"_class":"col-sm-4", "className":"col-sm-4"},
              $label( {'_class':'control-label',  'for':''}, '' ) ),
	 $div( {"_class":"col-sm-8", "className":"col-sm-8"},
              $div( {'_class':'controls', 'className':'controls'}, depositButton, ' ', cancelButton ) ) ) );
    

    var navigation = getAccountNavigation( accountName, accountNumber, 'depositButton', accountDetails );

    return  $div( {"_class":"row", "className":"row"},
			  $div( {"_class":"col-sm-12", "className":"col-sm-12"},
			       $div( {},
				    $div( {'style':'width:100%;float:left;margin-bottom:20px;margin-top:10px'},
					 $div( {"_class":"pull-left", "className":"pull-left"}, "Deposit for account " + accountName ), navigation ),
				    form ) ) );

}

var getWithdrawForm = function( accountName, accountNumber, accountDetails )
{
    console.log( 'Calling getWithdrawForm ()' );

    var withdrawButton = $button( {'type':'submit', '_class':'btn btn-primary', '_className':'btn btn-primary'}, 'Send Request' );
    var cancelButton = $button( {'type':'button', '_class':'btn btn-default', '_className':'btn btn-default'}, 'Cancel' );

    var balance = 0.00;
    if ( accountDetails.length > 0 ) {
	balance = accountDetails[accountDetails.length-1]['balance']; // last record contains the total balance
    }

	$( cancelButton ).on( 'click', handleStatementButtonClick( accountNumber, accountName, accountDetails ) );

	var withdrawFormCallback = function( withdrawButton ) {
		return function ( client ) {
			if ( !client['admin'] ) {
				$( withdrawButton ).on( 'click', function( e ) {
					e.preventDefault();
					alert( 'You must be administrator to make a withdraw.' );
				} );
			}
			else {
				$( withdrawButton ).on( 'click', submitWithdrawForm( accountName, accountNumber, balance ) );

			}
		}
	}

	getClientDetails( withdrawFormCallback( withdrawButton ) );

    var form = $form( {'id':'wpstocksWithdrawForm'} );
    $( form ).append( $div( {"_class":"row", "className":"row"},
			$div( {"_class":"col-sm-4", "className":"col-sm-4"},
			     $label( {'_class':'control-label',  'for':''}, 'Balance' ) ),
			$div( {"_class":"col-sm-8", "className":"col-sm-8"},
			     $div( {'_class':'controls', 'className':'controls'},
				  $input( {'disabled':true, '_class':'form-control', 'className':'form-control input-lg','placeholder':'', 'type':'text', 'id':'withdrawBalance', 'name':'balance', 'value':balance} ) ) ) ) );

    $( form ).append( $div( {"_class":"row", "className":"row"},
	 $div( {"_class":"col-sm-4", "className":"col-sm-4"},
              $label( {'_class':'control-label',  'for':''}, 'Withdraw' ) ),
	 $div( {"_class":"col-sm-8", "className":"col-sm-8"},
              $div( {'_class':'controls', 'className':'controls'},
                   $input( {'_class':'form-control', 'className':'form-control input-lg','placeholder':'', 'id':'withdrawAmount', 'type':'text', 'name':'withdrawAmount'} ) ) ),
			$div( {"_class":"col-sm-12", "className":"col-sm-12"},
			     $p( { "style" : "text-align: center"}, "Max 1000" ) ) ) );



    $( form ).append( $div( {"_class":"row", "className":"row"},
	 $div( {"_class":"col-sm-4", "className":"col-sm-4"},
              $label( {'_class':'control-label',  'for':''}, '' ) ),
	 $div( {"_class":"col-sm-8", "className":"col-sm-8"},
              $div( {'_class':'controls', 'className':'controls'}, withdrawButton, ' ', cancelButton ) ) ) );
    

    var navigation = getAccountNavigation( accountName, accountNumber, 'withdrawButton', accountDetails );

    return  $div( {"_class":"row", "className":"row"},
			  $div( {"_class":"col-sm-12", "className":"col-sm-12"},
			       $div( {},
				    $div( {'style':'width:100%;float:left;margin-bottom:20px;margin-top:10px'},
					 $div( {"_class":"pull-left", "className":"pull-left"}, "Withdraw for account " + accountName ), navigation ),
				    form ) ) );

}

var getAccountDetailsTable = function ( accountName, accountNumber, transactions, balance ) {

	console.log( 'Calling getAccountDetailsTable ()' );
	console.log( transactions );

	var body = $tbody( {} );
	var foot = $tfoot( {} );

	var i = null
	var row = null;

	if ( transactions.length == 0 ) {

		$( body ).append( $tr( $td( { 'colspan':'12', 'style':'text-align:left;' }, 'No transactions found' ) ) );

	} else {

		for ( i in  transactions ) {
			row = $tr( 
				$td( {"style": "text-transform: initial"}, transactions[i]['date'] + '' ),
				$td( {}, transactions[i]['stock_symbol'] + '' ),
				$td( {"_class": "number", "className": "number"}, transactions[i]['stock_quantity'] + '' ),
				$td( {"_class": "currency", "className": "currency"}, transactions[i]['stock_price'] + '' ),
				$td( {}, transactions[i]['ttnumber'] + '' == 'null' ? '' : transactions[i]['ttnumber']+'' ),
				$td( {}, transactions[i]['type'] + '' ),
				$td( {}, transactions[i]['reference'] + '' ),
				$td( {"_class": "currency", "className": "currency"}, transactions[i]['credit'] + ' '+transactions[i]['currency'] + '' ),
				$td( {"_class": "currency", "className": "currency"}, transactions[i]['debit'] + ' '+transactions[i]['currency'] + '' ),
				$td( {"_class": "currency", "className": "currency"}, transactions[i]['balance'] + ' '+ transactions[i]['currency'] +'' ),
				$td( {}, transactions[i]['status'] + '' )
			 );
			$( body ).append( row );
		}

	}

	row = $tr( {},
		$td( { "colspan" : "9", "_class" : "currency", "className" : "currency" }, "Total: " ),
		$td( { "colspan" : "2", "style":"text-align: center", "_class" : "currency centred", "className" : "currency centred" }, balance + ' '+ ( transactions.length > 0 ? transactions[0]['currency'] : 'USD' ) + '' ) );
	$( foot ).append( row );

    var navigation = getAccountNavigation( accountName, accountNumber, 'statementButton', transactions );

    return  $div( {"_class":"row", "className":"row"},
                  $div( {"_class":"col-sm-12", "className":"col-sm-12"},
		     $div( {'style':'width:100%;float:left;margin-bottom:20px; margin-top:10px;'}, $div( {"_class":"pull-left", "className":"pull-left"}, "Statement for account " + accountName ), navigation ),
                     $table( {"_class":"table", "className":"table"},
                        $thead( {},
                            $tr( {},
                               $th( {}, "Date / Time" ),
                               $th( {}, "Symbol" ),
                               $th( { "_class" : "number", "className" : "number" }, "Quantity" ),
                               $th( { "_class" : "currency", "className" : "currency" }, "Price" ),
                               $th( {}, "Ref. Number" ),
                               $th( {}, "Type" ),
                               $th( {}, "Reference" ),
                               $th( { "_class" : "currency", "className" : "currency" }, "Credit" ),
                               $th( { "_class" : "currency", "className" : "currency" }, "Debit" ),
                               $th( { "_class" : "currency", "className" : "currency" }, "Balance" ),
                               $th( {}, "Status" )
                               )
			 ),
                        body, foot ) ) );

}

var createAccount = function( fancybox, accountsTableBody )
{
    return function( e ){

	console.log( 'Calling createAccount ()' );

	$.ajax( wpstocks_js_parameters.ajax_url+'/?action=wpstocks_ajax_createAccount', {  // For POSTS must include trailing /
	    'type':'POST',
	    'error':( function( jqXHR, testStatus, errorThrown ){
		console.log( 'Error' );
		console.log( jqXHR );
		console.log( errorThrown );
	    } ),
	    'success':( function( data, textStatus, jqXHR ){
		console.log( 'Success fully create account' );
		console.log( jqXHR );
		$.fancybox.close ();
		alert( 'Account created' );
		var detailLink = $a( {'href':'#'}, $( '#wpstocks_accountName' ).val () + '' );

		getAccountDetailsCallback = renderAccountDetails( jqXHR.responseJSON['accountNumber'], $( '#wpstocks_accountName' ).val (), true );
		$( detailLink ).on( 'click', getAccountDetails( $( '#wpstocks_accountName' ).val (), jqXHR.responseJSON['accountNumber'], getAccountDetailsCallback ) );

//		$( detailLink ).on( 'click', handleAccountDetailsClick( jqXHR.responseJSON['accountNumber']+'', $( '#wpstocks_accountName' ).val () ) );
		var row = $tr( $td( {}, detailLink ), $td( {}, 'USD'), $td( { "_class" : "currency", "className" : "currency" },   ' 0.00 USD') );
		$( accountsTableBody ).append( row );
	    } ),
	    'statusCode':{
		404:function (){
		}
	    },
	    'accepts':'application/json',
	    'data':'wpstocks_accountName='+$( '#wpstocks_accountName' ).val (),
	    'dataType':'json'
	} );
    }
}

var addItemToWatchlist = function( callback )
{
	return function( e ){
		console.log( wpstocks_js_parameters.plugin_url+'api/watched/' );
		$.fancybox.close ();
		$.ajax( wpstocks_js_parameters.plugin_url+'api/watched/', {  // For POSTS must include trailing /
			'type':'POST',
			'error':( function( jqXHR, testStatus, errorThrown ){
				console.log( 'Error' );
				console.log( jqXHR );
				console.log( errorThrown );
			} ),
			'success':( function( data, textStatus, jqXHR ){
				console.log( 'Success' );
				console.log( jqXHR );
				callback( jqXHR );
			} ),
			'statusCode':{
				404:function (){
//		    alert( 'No stocks found' );
				}
			},
			'accepts':'application/json',
			'data':'username='+wpstocks_js_parameters.username+'&stock='+$( '#wpstocks_tickerCode' ).val ()+'&api=barchart&apikey=489911a5880b02d7093e260e0158fd39',
			'dataType':'json'
		} );
	}
}

var removeItemFromWatchlist = function( stock, callback )
{
    return function( e ){
	$.ajax( wpstocks_js_parameters.plugin_url+'api/watched/?username='+wpstocks_js_parameters.username+'&stock='+stock, {  // For POSTS must include trailing /
	    'type':'DELETE',
	    'error':( function( jqXHR, testStatus, errorThrown ){
		console.log( 'Error' );
		console.log( jqXHR );
		console.log( errorThrown );
	    } ),
	    'success':( function( data, textStatus, jqXHR ){
		console.log( 'Success' );
		console.log( jqXHR );
		callback( jqXHR );
	    } ),
	    'statusCode':{
		404:function (){
		}
	    },
	    'data':'username='+wpstocks_js_parameters.username+'&stock='+stock
	} );
    }
}

var getWatchList = function( callback )
{
    return function( e ){
	console.log( 'Getting watchlist' );
	$.ajax( wpstocks_js_parameters.plugin_url+'api/watched/', {  // For POSTS must include trailing /
	    'type':'GET',
	    'error':( function( jqXHR, testStatus, errorThrown ){
		console.log( 'Error' );
		console.log( jqXHR );
		console.log( errorThrown );
	    } ),
	    'success':( function( data, textStatus, jqXHR ){
		console.log( 'Success' );
		console.log( jqXHR );
		callback( jqXHR );
	    } ),
	    'statusCode':{
		404:function (){
//		    alert( 'No stocks found' );
		}
	    },
	    'accepts':'application/json',
	    'data':'username='+wpstocks_js_parameters.username+'&api=barchart&apikey=489911a5880b02d7093e260e0158fd39',
	    'dataType':'json'
	} );
    }
}

var register = function ()
{
    return function( e ){
	e.preventDefault (); //STOP default action
	if( e.target.checkValidity () ) {
	    if ( $( '#wpstocks_email' ).val () != $( '#wpstocks_confirmemail' ).val () ) {
		alert( 'Email fields are not the same' );
	    }
	    else if ( $( '#wpstocks_password' ).val () != $( '#wpstocks_confirmpassword' ).val () ) {
		alert( 'Password fields are not the same' );
	    }
	    else{
		var postData = $( this ).serialize ();;
		$.ajax( wpstocks_js_parameters.ajax_url+'/?action=wpstocks_ajaxRegister', {  // For POSTS must include trailing /
		    'type':'POST',
		    'error':( function( jqXHR, testStatus, errorThrown ){
			console.log( 'Error' );
			console.log( jqXHR );
			console.log( errorThrown );
		    } ),
		    'success':( function( data, textStatus, jqXHR ){
			console.log( 'Success' );
			console.log( jqXHR );
			alert( "Congratulations! You are now registered" );
			document.location.href = 'wp-login.php';
		    } ),
		    'statusCode':{
			409:function (){
			    alert( 'User name already exists' );
			},
			500:function (){
			    alert( 'Error. Possible duplicate email.' );
			}
		    },
		    'data':postData
		} );
	    }
	} else {
	    alert( 'Please fill in all fields' );
	}
    }
}

var getLatitudeAndLongitude = function( callback_fn ){
    return function( e ){
	if( navigator.geolocation ) {
	    console.log( 'Navigator:' );
	    console.log( navigator );
	    navigator.geolocation.getCurrentPosition( 
		getPositionCallback (),
		handleNoGeolocation( callback_fn ),{ enableHighAccuracy: true} );
	}
	else{
	    console.log( 'No navigator found' );
	}
    }
}

var getPositionCallback = function (){
    return function( position ){
	//    navigator.geolocation.watchPosition( function( position ) 
	var latitude = position.coords.latitude; 
	var longitude = position.coords.longitude;
	var geocode_fn = get_geocode_callback ();
	get_geocode( latitude+","+longitude, geocode_fn( latitude+","+longitude ) );
    }
}

var get_geocode = function( address, callback ){
    var geocoder = new google.maps.Geocoder ();
    var cb = callback( geocoder );
    geocoder.geocode( { 'address': address}, cb );
}

var get_geocode_callback = function (){
    return function( latlng ){
	return function( geocoder ){
	    return function( results, status ){
		if ( status == google.maps.GeocoderStatus.OK ) {
		    // 5 is country, 6 is post code
		    //		    var loc = results[0].address_components[1].short_name+', '+results[0].address_components[2].short_name+', '+results[0].address_components[3].short_name;
		    console.log( "Your location ( approx ): " )
		    console.log( results[0].address_components );
		    console.log( results );
		}
		else{
		    console.log( 'Geocode was not successful for the following reason: ' + status );
		}
	    }
	}
    }
}    

var handleNoGeolocation = function () {
    return function( errorFlag ){
	var content = '';
	if ( errorFlag ) {
	    console.log( 'Error: The Geolocation service failed.' );
	} else {
	    console.log( 'Error: Your browser doesn\'t support geolocation.' );
	}
    }
}

var open_media_dialog = function (){
    return function( e ){
	/*
	  See plugins/ytp/ for usage
	  Requires:
	  if( function_exists( 'wp_enqueue_media' ) ){
	  wp_enqueue_media ();
	  }else{
	  wp_enqueue_style( 'thickbox' );
	  wp_enqueue_script( 'media-upload' );
	  wp_enqueue_script( 'thickbox' );
	  }
	  Example:
          <div class="col-sm-7">
	  <?php
	  if( function_exists( 'wp_enqueue_media' ) ){
	  wp_enqueue_media ();
	  }else{
	  wp_enqueue_style( 'thickbox' );
	  wp_enqueue_script( 'media-upload' );
	  wp_enqueue_script( 'thickbox' );
	  }
	  ?>
	  <!-- http://stackoverflow.com/questions/13847714/wordpress-3-5-custom-media-upload-for-your-theme-options -->
          <button class="btn btn-primary btn-large custom_media_upload" type="button">Select image</button>
          <img class="custom_media_image" src="" style="width:100%;" />
          <input class="custom_media_image" value="" type="hidden" name="ytp_banner_imgsrc" />
          </div>
          </div>

	*/
	if( e ){
	    console.log( 'Preventing default action' );   
	    e.preventDefault (); //STOP default action
	}

	console.log( 'uploading image' );
	console.log( wp );

	var send_attachment_bkp = wp.media.editor.send.attachment;

	wp.media.editor.send.attachment = function( props, attachment ) {

            jQuery( '.custom_media_image' ).attr( 'src', attachment.url );
            jQuery( '.custom_media_url' ).val( attachment.url );
            jQuery( '.custom_media_id' ).val( attachment.id );

            wp.media.editor.send.attachment = send_attachment_bkp;
	}

	wp.media.editor.open ();

	return false;       

    }
}

var valid_url = function( str ) {
    var pattern = new RegExp( '^( https?:\\/\\/ )?'+ // protocol
			     '( ( ( [a-z\\d]( [a-z\\d-]*[a-z\\d] )* )\\. )+[a-z]{2,}|'+ // domain name
			     '( ( \\d{1,3}\\. ){3}\\d{1,3} ) )'+ // OR ip ( v4 ) address
			     '( \\:\\d+ )?( \\/[-a-z\\d%_.~+]* )*'+ // port and path
			     '( \\?[;&a-z\\d%_.~+=-]* )?'+ // query string
			     '( \\#[-a-z\\d_]* )?$','i' ); // fragment locator
    if( !pattern.test( str ) ) {
	return false;
    } else {
	return true;
    }
}

var getWatchlistCallback = function( container ) {
	return function( jqXHR ){
		var watchList = jqXHR.responseJSON;
		console.log( 'Got watch list:' );
		console.log( watchList );
		var i = null
		var buyButton = null;
		var removeButton = null;
		var row = null;
		for ( i in watchList ) {
			buyButton =  $button( {}, 'Buy' );
			removeButton =  $button( {}, 'Remove' );
			row = $tr( {},
				$td( {}, watchList[i]['symbol']+'' ),
				$td( {}, watchList[i]['name']+'' ),
				$td( { "_class" : "currency", "className" : "currency" }, watchList[i]['lastPrice'] + ' '+watchList[i]['currency'] + '' ),
				$td( {}, buyButton, ' ', removeButton ) );
			$( container ).append( row );
			$( buyButton ).on( 'click', getQuote( watchList[i]['symbol'] ) );
			$( removeButton ).on( 'click', removeItemFromWatchlist( watchList[i]['symbol'],removeItemFromWatchlistCallback( row ) ) );
		}
	}
}

var addItemToWatchlistCallback = function( container ) {
	return function( jqXHR ){

		console.log( 'container:' );
		console.log( container );
		var watchList = jqXHR.responseJSON;
		console.log( 'Got watch list item:' );
		console.log( watchList );
		var buyButton =  $button( {}, 'Buy' );
		var removeButton =  $button( {}, 'Remove' );
		var i = 0;
		var row = $tr( {},
			$td( {}, watchList[i]['symbol']+'' ),
			$td( {}, watchList[i]['name']+'' ),
			$td( { "_class" : "currency", "className" : "currency" }, watchList[i]['lastPrice']+' '+watchList[i]['currency']+'' ),
			$td( {}, buyButton, ' ', removeButton ) );

		$( buyButton ).on( 'click', getQuote( watchList[i]['symbol'] ) );
		$( removeButton ).on( 'click', removeItemFromWatchlist( watchList[i]['symbol'],removeItemFromWatchlistCallback( row ) ) );
		$( container ).append( row );
		console.log('Done');
	}
}

var removeItemFromWatchlistCallback = function( row )
{
    return function( jqXHR ){
	console.log( 'Item removed from watch list' );
	$( row ).remove ();
    }
}



