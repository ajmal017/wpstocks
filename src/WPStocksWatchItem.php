<?php

namespace premiumwebtechnologies\wpstocks;


class WPStocksWatchItem
{

    private $account_name;
    private $account_number;
    private $stock_price;
    private $stock_name;
    private $currency;

    /**
     * WPStocksStockItem constructor.
     */
    public function __construct( $account_name, $account_number, $stock_name, $stock_price, $currency ) {

        $this->account_number = $account_number;
        $this->account_name = $account_name;
        $this->stock_price = $stock_price;
        $this->stock_name = $stock_name;
        $this->currency = $currency;
    }

    /**
     * @return mixed
     */
    public function get_account_name()
    {
        return $this->account_name;
    }

    /**
     * @return mixed
     */
    public function get_account_number()
    {
        return $this->account_number;
    }

    /**
     * @return mixed
     */
    public function get_stock_price()
    {
        return trim( money_format( "%n", $this->stock_price ), '$' );
    }

    /**
     * @return mixed
     */
    public function get_stock_name()
    {
        return $this->stock_name;
    }

    /**
     * @return mixed
     */
    public function get_currency()
    {
        return $this->currency;
    }




}