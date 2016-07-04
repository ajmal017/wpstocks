<?php

namespace premiumwebtechnologies\wpstocks;


class WPStocksStock
{

    private $name;
    private $symbol;

    /**
     * WPStocksStock constructor.
     * @param $name
     * @param $symbol
     */
    public function __construct($name, $symbol)
    {
        $this->name = $name;
        $this->symbol = $symbol;
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
    public function get_symbol()
    {
        return $this->symbol;
    }



}