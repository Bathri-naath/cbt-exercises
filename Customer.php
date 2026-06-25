<?php

class Customer
{
    public string $customer_name;
    public int $mobile_number;
    public string $customer_id;
    public array $accounts;

    public function __construct(int $_customer_id, string $_customer_name, int $_mobile_number, array $_accounts)
    {
        $this->customer_name = $_customer_name;
        $this->mobile_number = $_mobile_number;
        $this->customer_id = $_customer_id;
        $this->accounts = $_accounts;
        $account = new Account($this->accounts);
    }


}