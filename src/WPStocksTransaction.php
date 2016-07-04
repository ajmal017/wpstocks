<?php

namespace premiumwebtechnologies\wpstocks;


class WPStocksTransaction
{

    private $type;
    private $amount;
    private $currency;
    private $name;
    private $stock_price;
    private $stock;
    private $stock_quantity;
    private $date;
    private $reference;
    private $ttnumber;
    private $status;

    /**
     * WPStocksTransaction constructor.
     */
    public function __construct( $type, $amount, $currency, $reference = '', $ttnumber = '', $name = 'SELL', $stock = null, $stock_price = 0, $stock_quantity = 0,  $status = 'complete', $date = null )
    {
        if ( empty( $date ) ) {
            $date = date( 'Y-m-d H:i:s' );
        }

        $this->type = $type;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->name = $name;
        $this->stock_price = $stock_price;
        $this->stock = $stock;
        $this->stock_quantity = $stock_quantity;
        $this->date = $date;
        $this->reference = $reference;
        $this->ttnumber = $ttnumber;
        $this->status = $status;
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
    public function get_amount()
    {
        return trim( money_format( "%n", empty( $this->amount ) ? 0.00 : trim( $this->amount, '$' ) * 1.00 ) , '$' );
    }

    /**
     * @return mixed
     */
    public function get_name()
    {
        return $this->name; // BUY, SELL
    }

    /**
     * @return mixed
     */
    public function get_stock_price()
    {
        return trim( money_format( "%n", empty( $this->stock_price ) ? 0.00 : trim( $this->stock_price, '$' ) * 1.00 ) , '$' );
    }

    /**
     * @return mixed
     */
    public function get_stock()
    {
        return $this->stock;
    }

    /**
     * @return mixed
     */
    public function get_currency()
    {
        return $this->currency;
    }

    /**
     * @return int
     */
    public function get_stock_quantity()
    {
        return $this->stock_quantity;
    }

    /**
     * @return null
     */
    public function get_date()
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function get_reference()
    {
        return $this->reference;
    }

    /**
     * @return string
     */
    public function get_ttnumber()
    {
        return $this->ttnumber;
    }

    /**
     * @return string
     */
    public function get_status()
    {
        return $this->status;
    }

    

}