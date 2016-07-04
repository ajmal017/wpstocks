<?php

namespace premiumwebtechnologies\wpstocks;


class WPStocksAccount
{

    private $ID;

    /**
     * WPStocksAccount constructor.
     * @param $account_name name for the account
     * @param $user_id user id of the user that owns this account
     * @param $currency - account currency, defaults to USD
     */
    public function __construct( $account_name, $user_id, $currency, $funds, $account_status, $account_type )
    {

        $args = array(
            'post_title'=>$account_name,
            'post_status'=>'publish',
            'comment_status'=>'closed',
            'post_type'=>'wpstocks_accspage'
        );

        $this->ID = wp_insert_post( $args );

        if ( ! add_post_meta( $this->ID, 'user_id', $user_id * 1, true ) ) {
            update_post_meta( $this->ID, 'user_id', $user_id *1 );
        }
        if ( ! add_post_meta( $this->ID, 'funds', $funds *1, true ) ) {
            update_post_meta( $this->ID, 'funds', $funds *1 );
        }
        if ( ! add_post_meta( $this->ID, 'account_status', $account_status, true ) ) {
            update_post_meta( $this->ID, 'account_status', $account_status );
        }
        if ( ! add_post_meta( $this->ID, 'account_type', $account_type, true ) ) {
            update_post_meta( $this->ID, 'account_type', $account_type );
        }

        $wpstocks_user = new WPStocksUser( $user_id );
        if ( ! add_post_meta( $this->ID, 'wpstocks_user', $wpstocks_user, true ) ) {
            update_post_meta( $this->ID, 'wpstocks_user', $wpstocks_user );
        }
       // $this->account_name = $account_name;
        if ( ! add_post_meta( $this->ID, 'currency', $currency, true ) ) {
            update_post_meta( $this->ID, 'currency', $currency );
        }

        $accounts = $wpstocks_user->get_accounts();
        if ( ! add_post_meta( $this->ID, 'account_number', count( $accounts ) + 1, true ) ) {
            update_post_meta( $this->ID, 'account_number', count( $accounts ) + 1 );
        }

    }

    public function add_funds( $funds )
    {
        $funds += get_post_meta( $this->ID, 'funds', true);
        if ( ! add_post_meta( $this->ID, 'funds', $funds *1, true ) ) {
            update_post_meta( $this->ID, 'funds', $funds *1 );
        }
    }

    public function make_deposit( $amount, $currency, $reference, $ttnumber) {
        $transaction = new WPStocksTransaction( 'credit', $amount, $currency, $reference, $ttnumber );
        $transactions = get_post_meta( $this->ID, 'transactions', true );
        $transactions[] = $transaction;
        if ( ! add_post_meta( $this->ID, 'transactions', $transactions, true ) ) {
            update_post_meta( $this->ID, 'transactions', $transactions );
        }
        $wpstocks_user = get_post_meta( $this->ID, 'wpstocks_user', true);
        $wpstocks_user->set_account( $this );
        if ( ! add_post_meta( $this->ID, 'wpstocks_user', $wpstocks_user, true ) ) {
            update_post_meta( $this->ID, 'wpstocks_user', $wpstocks_user );
        }
    }

    public function make_withdraw($amount, $currency, $reference, $ttnumber) {
        $transaction = new WPStocksTransaction( 'debit', $amount, $currency, $reference, $ttnumber );
        $transactions = get_post_meta( $this->ID, 'transactions', true );
        $transactions[] = $transaction;
        if ( ! add_post_meta( $this->ID, 'transactions', $transactions, true ) ) {
            update_post_meta( $this->ID, 'transactions', $transactions );
        }
        $wpstocks_user = get_post_meta( $this->ID, 'wpstocks_user', true);
        $wpstocks_user->set_account( $this );
        if ( ! add_post_meta( $this->ID, 'wpstocks_user', $wpstocks_user, true ) ) {
            update_post_meta( $this->ID, 'wpstocks_user', $wpstocks_user );
        }
    }
    
    /**
     * @param int $funds
     */
    private function update_funds( $funds )
    {
        if ( ! add_post_meta( $this->ID, 'funds', $funds *1, true ) ) {
            update_post_meta( $this->ID, 'funds', $funds *1 );
        }
        $wpstocks_user = get_post_meta( $this->ID, 'wpstocks_user', true);
        $wpstocks_user->set_account( $this );
        if ( ! add_post_meta( $this->ID, 'wpstocks_user', $wpstocks_user, true ) ) {
            update_post_meta( $this->ID, 'wpstocks_user', $wpstocks_user );
        }
    }

    /**
     * @return name
     */
    public function get_account_name()
    {
        $post = get_post( $this->ID );
        if ( !is_wp_error( $post ) && !empty( $post ) ) {
            return $post->post_title;
        }
        else{
            return null;
        }

    }

    public function get_balance( $account_number = null ) {
        
        $balance = $this->get_funds();
        $transactions = get_post_meta( $this->ID, 'transactions', true );
        if ( !empty( $transactions ) ) {

            if ( !empty( $account_number ) ) {
                $balance = $this->get_balance_by_account_number( $account_number );
            }
            else {
                foreach ( $transactions as $transaction ) {
                  //  $balance += $this->get_balance_by_account_number( $account_number );
                    if ('credit' == $transaction->get_type()) {
                        $balance += str_replace(',', '', $transaction->get_amount() ) * 1.00;
                    } elseif ('debit' == $transaction->get_type()) {
                        $balance -= str_replace(',', '', $transaction->get_amount() ) * 1.00;
                    }
                    if ('BUY' == $transaction->get_name()) {
                        $balance -= ( str_replace( ',', '', $transaction->get_stock_price() ) * 1.00 ) * $transaction->get_stock_quantity();
                    } elseif ('SELL' == $transaction->get_name()) {
                        // todo - stock price should be stock price at which it was sold
                       // $balance -= ( str_replace( ',', '', $transaction->get_stock_price() ) * 1.00 ) * $transaction->get_stock_quantity();
                    }
                }
            }
        }

        return trim( money_format( "%n", $balance ), '$' );

    }

    private function get_balance_by_account_number( $account_number ) {

        $balance = 0;
        $transactions = get_post_meta( $this->ID, 'transactions', true );

        for ($i = 0; $i < count($transactions[$account_number]); $i++) {
            if ('credit' == $transactions[$account_number][$i]->get_type()) {
                $balance += $transactions[$account_number][$i]->get_amount();
            } elseif ('debit' == $transactions[$account_number][$i]->get_type()) {
                $balance -= $transactions[$account_number][$i]->get_amount();
            }
        }

        return trim( money_format( "%n", $balance ), '$' );

    }

    public function add_transaction( $transaction ) {
        $transactions = get_post_meta( $this->ID, 'transactions', true );
        $transactions[] = $transaction;
        if ( ! add_post_meta( $this->ID, 'transactions', $transactions, true ) ) {
            update_post_meta( $this->ID, 'transactions', $transactions );
        }
    }
    /**
     * @return array
     */
    public function get_transactions()
    {
        return get_post_meta( $this->ID, 'transactions', true );
    }

    /**
     * @return int
     */
    public function get_account_number()
    {
        return get_post_meta( $this->ID, 'account_number', true );
    }

    /**
     * @return int
     */
    public function get_funds()
    {
        return trim( money_format( "%n", get_post_meta( $this->ID, 'funds', true ) * 1.00 ), '$' );
    }

    /**
     * @return mixed
     */
    public function get_currency()
    {
        return get_post_meta( $this->ID, 'currency', true );
    }


    

}