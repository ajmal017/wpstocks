<?php

namespace premiumwebtechnologies\wpstocks;


class WPStocksPortfolio
{

    private $user_id;
    private $portfolio_records;

    /**
     * WPStocksPortfolio constructor.
     * @param $user_id
     * @param $portfolio_records
     */
    public function __construct( $user_id, $portfolio_records)
    {
        $this->user_id = $user_id;
        $this->portfolio_records = $portfolio_records;
    }

    /**
     * @return mixed
     */
    public function get_user_id()
    {
        return $this->user_id;
    }

    /**
     * @return mixed
     */
    public function get_portfolio_records()
    {
        return $this->portfolio_records;
    }




}