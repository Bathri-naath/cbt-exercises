<?php

use Random\Randomizer;

require_once 'Customer.php';

class AccountService
{

    public $customers = [];
    public $total_amount;
    public $date_today;
    public object $accounts;
    private array $transactions = [];
    public $limit_json_path = __DIR__ . "/AccountLimit.json";
    public $customer_json_path = __DIR__ . '/AccountInformation.json';
    public $transaction_json_path = __DIR__ . '/AccountTransaction.json';
    public function __construct()
    {
        $this->date_today = date('d-m-Y');
        $customer_json_string = file_get_contents($this->customer_json_path);
        $customer_information = json_decode($customer_json_string);
        foreach ($customer_information as $customer) {

            $customer_details = new Customer();
            $customer_details->setCustomerId($customer->customer_id);
            $customer_details->setMobileNumber($customer->mobile_number);
            $customer_details->setCustomerName($customer->customer_name);
            $customer_details->setAccounts($customer->accounts);
            $this->customers[$customer->customer_id] = $customer_details;

        }
    }
    private function loadAccountDetails()
    {



    }


    private function loadTransactions()
    {
        $transaction_json_string = file_get_contents($this->transaction_json_path);
        $this->transactions = json_decode($transaction_json_string);
    }

    private function accountsLinkedToMobile(int $_mobile_number)
    {
        $account_count = 0;
        foreach ($this->customers as $account => $details) {
            if ($details->getMobileNumber() == $_mobile_number) {
                $account_count = count($details->getAccounts());
            }
        }
        return $account_count;
    }


    private function checkAccountNumber(int $_acc_num)
    {
        foreach ($this->customers as $account => $details) {
            if (in_array($_acc_num, array_column($details->getAccounts(), 'account_number'))) {
                return true;
            }
        }
    }

    private function validateCustomerId(int $_customer_id, int $_mobile_number)
    {
        foreach ($this->customers as $account => $details) {
            if ($details->getCustomerId() == $_customer_id && $details->getMobileNumber() == $_mobile_number) {
                return true;
            }
        }
    }

    private function saveAccountDetails(int $_account_number, string $_account_type, float $_account_balance, int $_customer_id, string $_banker_name, int $_banker_mobile_number)
    {
        $_customers = $this->customers;
        $new_account = new Account();
        $new_account->setAccountBalance($_account_balance);
        $new_account->setAccountType($_account_type);
        $new_account->setAccountNumber($_account_number);

        $new_account_details = $new_account;

        foreach ($this->customers as $customer_id => $customer_details) {
            if ($customer_id == $_customer_id) {
                // $customer_details->addAccount($_account_number, $new_account_details);
            }
        }
        // print_r($this->customers);
        // die;

        $new_customer_json_string = json_encode(array_values($this->customers), JSON_PRETTY_PRINT);
        echo $new_customer_json_string;
        die;

        file_put_contents($this->customer_json_path, $new_customer_json_string);
        echo "\nAccount created!";
        $this->displayAccountByAccountNumber($_account_number);
    }

    private function generateNewAccountNumber()
    {
        a:
        $new_account_number = (int) random_int(100000000, 999999999);
        if ($this->checkAccountNumber($new_account_number)) {
            goto a;
        } else {
            return $new_account_number;
        }
    }

    private function inputAccountDetails(int $_customer_id, int $_banker_mobile_number)
    {
        echo "\n";
        $input_banker_name = readline("Enter your name: ");

        echo "\n";
        $input_account_type = readline("Enter the type of account: ");


        $new_account_number = $this->generateNewAccountNumber();

        $this->saveAccountDetails($new_account_number, $input_account_type, 0.0, $_customer_id, $input_banker_name, $_banker_mobile_number);


    }

    private function getAccountDetailsByMobileNumber(int $_mobile)
    {

        foreach ($this->customers as $account => $details) {

            if ($details->getMobileNumber() == $_mobile) {
                $accounts = $details->getAccounts();
                $account_type = "";
                foreach ($accounts as $account_number => $account_details) {
                    $account_type = $account_details->getAccountType();
                }
                $customer_details = ["customer_name" => $details->getCustomerName(), "account_type" => $account_type, "customer_id" => $details->getCustomerId()];
                return $customer_details;
            }

        }

    }

    private function generateNewAccountType(string $_account_type)
    {
        $new_account_type = "";
        if ($_account_type === "Savings") {
            $new_account_type = "Current";
        } else {
            $new_account_type = "Savings";
        }
        return $new_account_type;
    }

    private function displayAllAccounts()
    {
        foreach ($this->customers as $customer_id => $customer_details) {
            echo "\nCustomer ID: " . $customer_details->getCustomerId() . "\nAccount Holder Name: " . $customer_details->getCustomerName() . "\tMobile Number: " . $customer_details->getMobileNumber();
            foreach ($customer_details->getAccounts() as $account => $account_details) {
                echo "\n\nAccount Number: " . $account_details->getAccountNumber() . "\nAccount Type: " . $account_details->getAccountType() . "\nAccount Balance: " . $account_details->getAccountBalance() . "\n";
            }
        }
    }


    private function displayAccountByMobileNumber(int $_mobile_number)
    {

        foreach ($this->customers as $account => $details) {

            if ($details->getMobileNumber() == $_mobile_number) {
                echo "\nCustomer ID:" . $details->getCustomerId() . "\nAccount holder name: " . $details->getCustomerName() . "\nMobile number: " . $details->getMobileNumber() . "\n\n";
                echo implode(array_map(function ($account) {
                    return "Account Number: " . $account->getAccountNumber() . "\t" .
                        "Account Type: " . $account->getAccountType() . "\n" .
                        "Account Balance: " . $account->getAccountBalance() . "\n";
                }, $details->getAccounts()));
            }

        }

    }

    private function displayAccountByAccountNumber(int $_account_number)
    {
        foreach ($this->customers as $customer_id => $customer_details) {
            $retrieved_accounts = $customer_details->getAccounts();
            if (array_key_exists($_account_number, $retrieved_accounts)) {
                echo "\nCustomer ID: " . $customer_details->getCustomerId() . "\nAccount Holder Name: " . $customer_details->getCustomerName() . "\tMobile Number: " . $customer_details->getMobileNumber();
                echo "\n\nAccount Number: " . $retrieved_accounts[$_account_number]->getAccountNumber() . "\tAccount Type: " . $retrieved_accounts[$_account_number]->getAccountType() . "\nAccount Balance: " . $retrieved_accounts[$_account_number]->getAccountBalance() . "\n";
            }
        }
    }

    private function addNewAccount()
    {
        $input_mobile_number = (int) readline("Enter your mobile number: ");
        $account_count = $this->accountsLinkedToMobile($input_mobile_number);
        if ($account_count == 1) {
            echo "\nThis user already exists!\n";
            $this->displayAccountByMobileNumber($input_mobile_number);
            echo "\nHowever you still can add another account of different type\n";
            $details = $this->getAccountDetailsByMobileNumber($input_mobile_number);
            $account_number = $this->generateNewAccountNumber();
            $new_account_type = $this->generateNewAccountType($details['account_type']);
            $this->saveAccountDetails($account_number, $new_account_type, 0.0, $details['customer_id'], $details['customer_name'], $input_mobile_number);
        } elseif ($account_count == 2) {
            echo "\nThis user already exists!\n";
            $this->displayAccountByMobileNumber($input_mobile_number);
        } elseif ($account_count == 0) {
            gen_cust_id:
            $new_customer_id = random_int(1000, 9999);
            if ($this->validateCustomerId($new_customer_id, $input_mobile_number)) {
                goto gen_cust_id;
            } else {
                $this->inputAccountDetails($new_customer_id, $input_mobile_number);
            }
        }
    }


    private function checkTransactions(int $_acc_num)
    {

        $this->loadTransactions();
        foreach ($this->transactions as $id => $transaction) {
            if ($transaction->date == $this->date_today && $transaction->account_number == $_acc_num) {
                return false;
                break;
            }
        }
        return true;

    }

    private function canDeposit(string $_account_type, float $_amount)
    {
        $limit_json_string = file_get_contents($this->limit_json_path);
        $limits = json_decode($limit_json_string);
        foreach ($limits as $type => $limit) {
            if ($type == $_account_type) {
                if ($_amount <= $limit) {
                    return true;
                } else {
                    return false;
                }
            }
        }
    }

    private function getAccountType(int $_acc_num)
    {

        foreach ($this->accounts as $id => $account) {
            if ($account->account->num == $_acc_num) {
                return $account->account->type;
            }
        }

    }

    private function checkDailyLimit(int $_acc_num)
    {

        $this->loadTransactions();
        $total_amount = 0.0;
        foreach ($this->transactions as $id => $transaction) {
            if ($this->date_today == $transaction->date && $transaction->account_number == $_acc_num) {
                $total_amount += $transaction->amount;
            }
        }
        $this->total_amount = $total_amount;
        $account_type = $this->getAccountType($_acc_num);
        if ($this->canDeposit($account_type, $total_amount)) {
            return true;
        } else {
            return false;
        }

    }


    private function getBalance(int $_acc_num)
    {

        foreach ($this->accounts as $account => $details) {
            if ($account == $_acc_num) {
                return $details->account->bal;
            }
        }

    }


    private function updateBalance(int $_acc_num, float $_amount)
    {

        foreach ($this->accounts as $account => $details) {
            if ($account == $_acc_num) {
                $details->account->bal = $_amount;
                $json = json_encode($this->accounts);
                file_put_contents($this->customer_json_path, $json);
                $this->loadAccountDetails();
                break;
            }
        }

    }


    private function deposit()
    {

        acc_num_inp:
        $input_account_number = (int) readline("Enter the account number: ");
        if ($this->checkAccountNumber($input_account_number)) {

            $this->displayAccountByAccountNumber($input_account_number);
            amt_inp:
            $input_amount = (float) readline("Enter the amount to be deposited: ");
            $eligibility = $this->checkTransactions($input_account_number);
            if ($eligibility == true) {
                updt_bal:
                $existing_account_bal = $this->getBalance($input_account_number);
                $existing_account_bal += $input_amount;
                $this->updateBalance($input_account_number, $existing_account_bal);
                $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $randomizer = new Randomizer();
                $transaction_id = $randomizer->getBytesFromString($chars, 10);
                $this->transactions->$transaction_id = [
                    "account_number" => $input_account_number,
                    "amount" => $input_amount,
                    "date" => $this->date_today
                ];
                $json = json_encode($this->transactions);
                file_put_contents($this->transaction_json_path, $json);
                $this->loadTransactions();
            } else {
                $daily_limit = $this->checkDailyLimit($input_account_number);
                if ($daily_limit == true) {
                    $account_type = $this->getAccountType($input_account_number);
                    $total_amount = $this->total_amount + $input_amount;
                    if ($this->canDeposit($account_type, $total_amount) == true) {
                        goto updt_bal;
                    } else {
                        echo "\nEnter an amount below the limit!\n";
                        goto amt_inp;
                    }
                } else {
                    echo "\nYou have reached the day's limit for this account\n";
                }

            }


        } else {
            echo "\n This account doesn't exist!";
            goto acc_num_inp;
        }

    }

    private function closeAccount()
    {

        $input_account_number = (int) readline("Enter the account number to be removed: ");
        if ($this->checkAccountNumber($input_account_number)) {

            $this->displayAccountByAccountNumber($input_account_number);
            unset($this->accounts->$input_account_number);
            $json = json_encode($this->accounts);
            file_put_contents($this->customer_json_path, $json);
            $this->loadAccountDetails();
            echo "\n\nAccount Deleted!\n";

        } else {
            echo "\nThis account number does not exists!\n";
        }

    }

    public function loadCustomer(object $_customer)
    {

    }

    public function run()
    {

        // var_dump($this->customers);
        do {

            echo "\n1. Display all accounts\n2. Add another account\n3. Deposit money in an account\n4. Close an Account\n5. Exit\n\n";
            $choice = (int) readline("Enter your choice: ");
            switch ($choice) {

                case 1:
                    $this->displayAllAccounts();
                    break;

                case 2:
                    $this->addNewAccount();
                    break;

                case 3:
                    $this->deposit();
                    break;
                case 4:
                    $this->closeAccount();
                    break;

                case 5:
                    echo "\nThank You!";

            }

        } while ($choice < 5);

    }

}