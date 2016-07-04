<?php

namespace premiumwebtechnologies\wpstocks; // use vendorname\subnamespace\classname;
    
/**
 * Class WPStocksUser
 * @package premiumwebtechnologies\wpstocks
 */
class WPStocksUser extends \WP_User
{
    private $company;
    private $tel;
    private $fax;
    private $address1;
    private $address2;
    private $address3;
    private $address4;
    private $city;
    private $country;
    private $secondary_email;
    private $home_phone;
    private $mobile;
    private $business_phone;
    private $office_phone;
    private $logo;

    /**
     * WPStocksUser constructor.
     * @param $id wordpress user id
     */
    public function __construct( $id ) {
        parent::__construct( $id );
    }

    public function set_account( $account ) {

        $accountName = $account->get_account_name();

        $accounts = $this->get_accounts();

        $accounts[ $accountName  ] = $account; // $account is an instance of WPStocksAccount

        $added = update_user_meta( $this->id, 'accounts', $accounts );
        if ( $added != true ) {
            $added = add_user_meta( $this->id, 'accounts', $accounts );
        }

        return $added;
    }

    public function get_accounts() {
        $accounts = get_the_author_meta( 'accounts', $this->ID );
        return !empty( $accounts ) ? $accounts : '';
    }

    /**
     * @return mixed
     */
    public function get_company() {
        $company = get_the_author_meta( 'company', $this->ID );
        return !empty( $company ) ? $company : '';
    }

    /**
     * @return mixed
     */
    public function get_fax() {
        $fax = get_the_author_meta( 'fax', $this->ID );
        return !empty( $fax ) ? $fax : '';
    }

    /**
     * @return mixed
     */
    public function get_address1() {
        $address1 = get_the_author_meta( 'address1', $this->ID );
        return !empty( $address1 ) ? $address1 : '';
    }

    /**
     * @return mixed
     */
    public function get_tel() {
        $tel = get_the_author_meta( 'tel', $this->ID );
        return !empty( $tel ) ? $tel : '';
    }

    /**
     * @return mixed
     */
    public function get_city() {
        $city = get_the_author_meta( 'city', $this->ID );
        return !empty( $city ) ? $city : '';
    }

    /**
     * @return mixed
     */
    public function get_country() {
        $country = get_the_author_meta( 'country', $this->ID );
        return !empty( $country ) ? $country : '';
    }

    public function get_portfolio() {

        $accounts = $this->get_accounts();
        $portfolio = null;

        $portfolio_records = array();

        foreach ( $accounts as $account ) {

            $transactions = $account->get_transactions();

            foreach ( $transactions as $account_number=>$transaction ) {

                if ( in_array( $transaction->get_name(), array( 'BUY', 'SELL' ) ) ) {

                    $stock = $transaction->get_stock();
                    if (!empty($stock)) {
                        $portfolio_records[] = new WPStocksPortfolioRecord($account->get_account_name(), $account_number, $transaction->get_amount(), $transaction->get_type(), $transaction->get_name(), empty($stock) ? $stock : $stock->get_symbol(), $transaction->get_stock_price(), $transaction->get_stock_quantity(), $transaction->get_currency());
                    }

                }

            }

            $portfolio = new WPStocksPortfolio( $this->ID, $portfolio_records );


        }


        return $portfolio;

    }


    /**
     * @return mixed
     */
    public function get_secondary_email()
    {
        $secondaryemail = get_the_author_meta( 'secondaryemail', $this->ID );
        return !empty( $secondaryemail ) ? $secondaryemail : '';
    }

    /**
     * @return mixed
     */
    public function get_home_phone()
    {
        $homephone = get_the_author_meta( 'homephone', $this->ID );
        return !empty( $homephone ) ? $homephone : '';
    }

    /**
     * @return mixed
     */
    public function get_office_phone()
    {
        $officephone = get_the_author_meta( 'officephone', $this->ID );
        return !empty( $officephone ) ? $officephone : '';
    }

    /**
     * @return mixed
     */
    public function get_business_phone()
    {
        $businessphone = get_the_author_meta( 'businessphone', $this->ID );
        return !empty( $businessphone ) ? $businessphone : '';
    }

    /**
     * @return mixed
     */
    public function get_mobile()
    {
        $mobile = get_the_author_meta( 'mobile', $this->ID );
        return !empty( $mobile ) ? $mobile : '';
    }

    /**
     * @return mixed
     */
    public function get_logo()
    {
        $logo = get_the_author_meta( 'logo', $this->ID );
        return !empty( $logo ) ? $logo : '';
    }

    /**
     * @return mixed
     */
    public function get_address2()
    {
        $address2 = get_the_author_meta( 'address2', $this->ID );
        return !empty( $address2 ) ? $address2 : '';
    }

    /**
     * @return mixed
     */
    public function get_address3()
    {
        $address3 = get_the_author_meta( 'address3', $this->ID );
        return !empty( $address3 ) ? $address3 : '';
    }

    /**
     * @return mixed
     */
    public function get_address4()
    {
        $address4 = get_the_author_meta( 'address4', $this->ID );
        return !empty( $address4 ) ? $address4 : '';
    }


}