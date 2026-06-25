<?php

class Account
{
    private int $account_number;
    private string $account_type;
    private float $account_balance;



    public function getAccountNumber(): int
    {
        return $this->account_number;
    }

    public function getAccountType(): string
    {
        return $this->account_type;
    }

    public function getAccountBalance(): float
    {
        return $this->account_balance;
    }

    public function setAccountNumber(int $account_number): void
    {
        $this->account_number = $account_number;
    }

    public function setAccountBalance($account_balance): void
    {
        $this->account_balance = $account_balance;
    }

    public function setAccountType(string $account_type): void
    {
        $this->account_type = $account_type;
    }

    public function jsonSerialize(): array
    {
        return [
            "account_number" => $this->account_number,
            "account_type" => $this->account_type,
            "account_balance" => $this->account_balance
        ];
    }

}

