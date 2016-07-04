<?php

namespace premiumwebtechnologies\wpstocks;


class WPStocksPortfolioRecord
{

    private $account_name;
    private $account_number;
    private $amount;
    private $type;
    private $name;
    private $stock_price;
    private $stock_name;
    private $stock_quantity;
    private $currency;

    /**
     * WPStocksPortfolioRecord constructor.
     */
    public function __construct( $account_name, $account_number, $amount, $type, $name, $stock_name, $stock_price, $stock_quantity, $currency ) {

        $this->account_number = $account_number;
        $this->account_name = $account_name;
        $this->amount = $amount;
        $this->type = $type; // debit or credit
        $this->name = $name; // BUY or SELL
        $this->stock_price = $stock_price;
        $this->stock_name = $stock_name;
        $this->stock_quantity = $stock_quantity;
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
    public function get_amount()
    {
        return trim( money_format( "%n", empty( $this->amount ) ? 0.00 : trim( $this->amount, '$' ) * 1.00 ) , '$' );
    }

    /**
     * @return mixed
     */
    public function get_type()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function get_name()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function get_stock_price()
    {
        return trim( money_format( "%n", empty( $this->stock_price ) ? 0.00 : trim( $this->stock_price, '$' ) * 1.00 ), '$' );
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
    public function get_stock_quantity()
    {
        return $this->stock_quantity;
    }

    /**
     * @return mixed
     */
    public function get_currency()
    {
        return $this->currency;
    }




}