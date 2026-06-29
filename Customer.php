<?php

class Customer implements JsonSerializable
{
    private string $customer_name;
    private int $mobile_number;
    private int $customer_id;
    private array $accounts = [];

    public function getCustomerName(): string
    {
        return $this->customer_name;
    }

    public function getMobileNumber(): int
    {
        return $this->mobile_number;
    }

    public function getCustomerId(): int
    {
        return $this->customer_id;
    }

    public function getAccounts(): array
    {
        return $this->accounts;
    }

    public function setCustomerName(string $_customer_name): void
    {
        $this->customer_name = $_customer_name;
    }

    public function setMobileNumber(int $_mobile_number): void
    {
        $this->mobile_number = $_mobile_number;
    }

    public function setCustomerId(int $_customer_id): void
    {
        $this->customer_id = $_customer_id;
    }

    public function setAccounts(array $_accounts): void
    {
        foreach ($_accounts as $account_id => $details) {
            $account_details = new Account();
            $account_details->setAccountNumber($details->account_number);
            $account_details->setAccountBalance($details->account_balance);
            $account_details->setAccountType($details->account_type);
            $this->accounts[] = $account_details;
        }
    }

    public function loadAccount(array $_account): void
    {
        $this->accounts = [...$_account];
    }

    public function addAccount(object $_account): void
    {
        $this->accounts[$_account->getAccountNumber()] = $_account;
    }

    public function jsonSerialize(): array
    {
        return [
            "customer_id" => $this->customer_id,
            "customer_name" => $this->customer_name,
            "mobile_number" => $this->mobile_number,
            "accounts" => $this->accounts
        ];
    }


    public function removeAccount(int $_account_number){
        foreach($this->accounts as $key=>$accounts){
            if($accounts->getAccountNumber()==$_account_number){
                unset($this->accounts[$key]);
                $this->accounts = array_values($this->accounts);
            }
        }
    }

}