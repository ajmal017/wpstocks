<?php

function wpstocks_ajax_getStock() {

    // http://localhost:8888/wp/wp-content/plugins/wpstocks/api/?stock=IBM&api=barchart&apikey=489911a5880b02d7093e260e0158fd39 GET
    if ( !isset( $_GET['stock']) || !isset( $_GET['apikey'])) {
        http_response_code( 400);
    } else {
        $stock = $_GET['stock'];
        $apikey = $_GET['apikey'];
        $url = plugin_dir_url( __FILE__) . "api/?stock=$stock&api=barchart&apikey=$apikey";
        //    echo $url;
        $res = pwt_getRemoteFileCurl( $url, "POST", array( ));
        echo( $res['content']);
    }
}


function wpstocks_ajax_getTransactions() {
    
    if ( !isset( $_GET['accountName'] ) ) {
        http_response_code( 400 );
    }

    include(ABSPATH . "wp-includes/pluggable.php");
    if (is_user_logged_in()) {

        $current_user = wpstocks_get_user( false );

        if (!is_object( $current_user ) ) {
            http_response_code( 403 );
        } else {

            $user_id = $current_user->ID;
            $accounts = $current_user->get_accounts();

            if ( empty( $accounts ) || !isset( $accounts[$_GET['accountName']] ) ) {
                http_response_code( 404 );
            }
            else {

                $account = $accounts[$_GET['accountName']];
                $transactions = $account->get_transactions();

                // Prepare transactions for json
                $parsedTransactions = array();
                $balance = $account->get_funds();
                if ( !empty( $transactions ) ) {
                    foreach ($transactions as $transaction) {
                        // name is BUY/SELL or empty
                        // amount only applies to debit/credit transactions
                        // type is debit/credit or empty
                        if ('debit' == $transaction->get_type()) {
                            $debit = str_replace( ',', '', $transaction->get_amount() ) * 1.00;
                            $balance -= $debit;
                        } else if ('credit' == $transaction->get_type()) {
                            $credit = str_replace( ',', '', $transaction->get_amount() ) * 1.00;
                            $balance += $credit * 1.00;
                        }
                        if ( 'BUY' == $transaction->get_name() ) {
                            $balance -= ( str_replace( ',', '', $transaction->get_stock_price() ) * 1.00 ) * $transaction->get_stock_quantity();
                        }
                        else if ( 'SELL' == $transaction->get_name() ) {
                            // todo - stock price should be current stock price
                            // $balance -= ( str_replace( ',', '', $transaction->get_stock_price() ) * 1.00 ) * $transaction->get_stock_quantity();
                        }

                        array_push($parsedTransactions, parseTransaction($transaction, $balance));
                    }
                }
                echo json_encode(array('user_id' => $user_id, 'accountName'=> $_GET['accountName'], 'balance'=>$balance, 'transactions' => $parsedTransactions ) );
            }

        }
    } else {
        http_response_code(403);
    }
    die();
}

function wpstocks_ajax_getAccounts() {

    include(ABSPATH . "wp-includes/pluggable.php");
    if (is_user_logged_in()) {

        $current_user = wpstocks_get_user( false );

        if (!is_object( $current_user ) ) {
            http_response_code( 403 );
        } else {

            $user_id = $current_user->ID;
            $accounts = $current_user->get_accounts();

            if ( empty( $accounts )) {
                http_response_code( 404 );
            }
            else {
                // Prepare accounts for json
                $parsedAccounts = array();
                foreach ( $accounts as $account ) {
                    // Only get accounts which have associated posts
                    $account_name = $account->get_account_name();
                    if ( !empty( $account_name ) ) {
                        $parsedAccounts[$account->get_account_name()] = array('currency' => $account->get_currency(), 'funds' => $account->get_funds(), 'balance' => $account->get_balance(), 'accountNumber' => $account->get_account_number());
                    }
                }
                echo json_encode(array('user_id' => $user_id, 'accounts' => $parsedAccounts ) );
            }

        }
    } else {
        http_response_code(403);
    }
    die();
}

function wpstocks_ajax_getPortfolio()
{

    include(ABSPATH . "wp-includes/pluggable.php");
    if (is_user_logged_in()) {
        $current_user = wpstocks_get_user(false);
        $portfolio = $current_user->get_portfolio();
        if (empty( $portfolio ) ) {
            http_response_code(404);
        } else {
            echo json_encode( parsePortfolio( $portfolio ) );
        }
    } else {
        http_response_code( 403 );
    }
    die();

}

function wpstocks_ajax_openPosition()
{

    // todo remove this
    if (empty( $_POST ) ){
        $_POST =  $_GET;
    }

    if ( !isset( $_POST['accountNumber'] ) || !isset( $_POST['accountName'] ) || !isset( $_POST['amount'] ) || !isset( $_POST['stock'] ) || !isset( $_POST['quantity'] ) || !isset( $_POST['price'] ) ) {
        http_response_code( 400 );
        die(  );
    }

    $stock = new \premiumwebtechnologies\wpstocks\WPStocksStock(  $_POST['stock'], $_POST['stock']  );
    $accountNumber = $_POST['accountNumber'];
    $accountName = $_POST['accountName'];
    $amount = $_POST['amount'];
    $date = date( "Y-m-d H:i:s" );
    $reference = $_POST['stock'];
    $ttnumber = '';
    $symbol = $_POST['stock'];
    $quantity = $_POST['quantity'];
    $price = empty( $_POST['price'] ) ? 0 : trim( $_POST['price'], '$' ) * 1;
    $status = 'Complete';


    include( ABSPATH . "wp-includes/pluggable.php" );


    if ( is_user_logged_in() ) {

        $current_user = wpstocks_get_user( true );

        $transaction = new \premiumwebtechnologies\wpstocks\WPStocksTransaction( '', 0, 'USD', $reference, $ttnumber, 'BUY', $stock, $price, $quantity, $status, $date );

        $accounts = $current_user->get_accounts();

        if ( !isset( $accounts[ $accountName ] ) ) {
            http_response_code( 404 );
            echo 'Account not found';
            die();
        }
        $account = $accounts[ $accountName ];

        $account->add_transaction( $transaction );


        if ( !$current_user->set_account( $account ) ){
            http_response_code( 407 );
            echo "Unable to set account";
            die();
        }

        $accounts = $current_user->get_accounts();
        $account = $accounts[ $accountName ];

        $parsedTransaction = parseTransaction( $transaction, $account->get_funds() );
        echo json_encode( $parsedTransaction );

    } else {
        http_response_code( 403 );
    }
    die();

}

function wpstocks_ajax_makeDeposit()
{

    if (!isset($_POST['accountNumber']) || !isset($_POST['accountName']) || !isset($_POST['amount']) || !isset($_POST['ttnumber'])) {
        http_response_code(400);
        die();
    }

    $accountNumber = $_POST['accountNumber'];
    $accountName = $_POST['accountName'];
    $amount = $_POST['amount'];
    $date = date("Y-m-d H:i:s");
    $reference = 'Deposit';
    $ttnumber = '';

    include(ABSPATH . "wp-includes/pluggable.php");
    if (is_user_logged_in()) {

        $current_user = wpstocks_get_user( true );
        $accounts = $current_user->get_accounts();
        $accounts[$_POST['accountName']]->make_deposit( $_POST['amount'], 'USD', '', $_POST['ttnumber']);

    } else {
        http_response_code(403);
    }
    die();
}

function wpstocks_ajax_makeWithdraw() {

    if ( !isset($_POST['accountNumber']) || !isset($_POST['accountName']) || !isset($_POST['amount']) ) {
        http_response_code( 400 );
        die();
    }

    $accountNumber = $_POST['accountNumber'];
    $accountName = $_POST['accountName'];
    $amount = $_POST['amount'];
    $date = date("Y-m-d H:i:s");
    $reference = 'Withdraw';

    include(ABSPATH . "wp-includes/pluggable.php");
    if (is_user_logged_in()) {

        $current_user = wpstocks_get_user( true );
        $accounts = $current_user->get_accounts();
        $accounts[$_POST['accountName']]->make_withdraw( $_POST['amount'], 'USD', '', '' );

    } else {
        http_response_code( 403 );
    }
    die();
}

function wpstocks_ajax_getAccountDetails()
{
    $account_number = $_GET['accountNumber'];
    $account_name = $_GET['accountName'];

    include(ABSPATH . "wp-includes/pluggable.php");

    if (is_user_logged_in()) {

        $current_user = wpstocks_get_user( false );
        $accounts = $current_user->get_accounts();

        if ( isset( $accounts[$account_name] ) ) {
            $account = $accounts[$account_name];
            echo json_encode( array( 'user_id'=>$current_user->ID, 'account_number'=>$account_number, 'account_name'=>$account->get_account_name(), 'balance'=> money_format('%n', $account->get_balance( [$account_number] ) ) ) );
        } else {
            http_response_code( 404 );
        }

    }

    die();

}

function wpstocks_ajax_getClientDetails() {
    include(ABSPATH . "wp-includes/pluggable.php");
    if ( is_user_logged_in() ) {
        $current_user = wpstocks_get_user( false );
        echo json_encode( array('admin'=>current_user_can( 'manage_options' ), "name" => $current_user->display_name, "country" => $current_user->get_country(), "address1" => $current_user->get_address1(), "address2" => $current_user->get_address2(),"address3" => $current_user->get_address3(), "address4" => $current_user->get_address4(), "telephone" => $current_user->get_tel(), "fax" => $current_user->get_fax(), "company"=> $current_user->get_company(), "email"=>$current_user->user_email, "businessphone"=>$current_user->get_business_phone(), 'homephone'=>$current_user->get_home_phone(), 'secondaryemail'=>$current_user->get_secondary_email(), 'officephone'=>$current_user->get_office_phone(), 'mobile'=>$current_user->get_mobile(), 'city'=>$current_user->get_city() ) );
    }
    die();
}

function wpstocks_ajax_createAccount()
{
    if ( !isset( $_POST['wpstocks_accountName']) || empty( $_POST['wpstocks_accountName'] ) ) {
        http_response_code( 400 );
        die();
    } else {

        include( ABSPATH . "wp-includes/pluggable.php" );
        if (is_user_logged_in()) {

            $current_user = wpstocks_get_user( true );
            $userID = $current_user->ID;

            //     public function __construct( $account_name, $user_id, $currency, $funds, $account_status, $account_type )
            $account = new \premiumwebtechnologies\wpstocks\WPStocksAccount( $_POST['wpstocks_accountName'], $userID, 'USD', 0, 'active', 'individual' );

            $current_user->set_account( $account );

            echo json_encode( array( 'accountName' => $account->get_account_name(), 'accountNumber' => $account->get_account_number(), 'amount' => 0 ) );

        }

    }

    die();
}

function wpstocks_ajaxRegister()
{

    wpstocks_insert_user( $_POST['wpstocks_password'], $_POST['wpstocks_username'], $_POST['wpstocks_firstname'], $_POST['wpstocks_lastname'], $_POST['wpstocks_email'] );
    die();

}

