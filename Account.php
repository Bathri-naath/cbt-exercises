<?php

class Account
{
    public int $account_number;
    public string $account_type;
    public float $account_balance;

    public function __construct(array $accounts)
    {
        foreach ($accounts as $account) {
            $this->account_number = $account->account_number;
            $this->account_type = $account->account_type;
            $this->account_balance = $account->account_balance;
        }
    }

}

