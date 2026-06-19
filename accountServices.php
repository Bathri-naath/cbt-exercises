<?php

use Random\Randomizer;

class accountServices
{

    public $saving_limit = 50000.0;
    public $current_limit = 100000.0;
    public $total_amount;
    public $accounts;
    private $transactions;
    public $account_json_path = __DIR__ . '/Accounts.json';
    public $transaction_json_path = __DIR__ . '/Transactions.json';

    /* 
     *Loads the details of the account from Accounts.json file to $accounts as an array of objects
     */

    private function loadAccountDetails()
    {


        $account_json_string = file_get_contents($this->account_json_path);
        $this->accounts = json_decode($account_json_string);

    }

    /* 
     *Loads all the transactions from Transactions.json file to $transactions as an array of objects
     */

    private function loadTransactions()
    {


        $transaction_json_string = file_get_contents($this->transaction_json_path);
        $this->transactions = json_decode($transaction_json_string);

    }

    /* 
    *Checks if the given mobile number is available in the stored data of accounts.

    *If it exists, the frequency of the same mobile number is stored and returned.

    *Used to check how many accounts are present for a user with the help of their mobile number. 

    @params int $_mob

    @return int $count
    */

    private function checkMobile($_mob)
    {

        $account_count = 0;
        foreach ($this->accounts as $account => $details) {
            if ($details->customer->mobile == $_mob) {
                $account_count++;
            }

        }
        return $account_count;

    }

    /* 
    *Checks if the given account number is present in the stored data of accounts

    @param int $_acc_num

    @return bool
    */

    private function checkAccountNumber($_acc_num)
    {

        foreach ($this->accounts as $account => $details) {
            if ($details->account->num == $_acc_num) {
                return true;
            }

        }

    }

    private function checkCustomerId($_customer_id, $_mobile)
    {

        foreach ($this->accounts as $account => $details) {

            if ($details->customer->cust_id == $_customer_id && $details->customer->mobile == $_mobile) {
                return true;
            }

        }

    }
    /* 
    *Stores the given data into the data of accounts and reloads the details from Accounts.json to $account as an array of objects.

    *Displays the stored account by calling displayAccountByAccountNumber()

    @param int $_acc_num
    @param string $_type
    @param float $_bal
    @param int $_cust_id
    @param string $_name
    @param int $mob
    */

    private function store($_acc_num, $_type, $_bal, $_cust_id, $_name, $_mob)
    {

        $account_number = $_acc_num;
        $_acc_num = (object) [
            "account" => [
                "num" => $_acc_num,
                "type" => $_type,
                "bal" => $_bal,
                "cust_id" => $_cust_id
            ],
            "customer" => [
                "cust_id" => $_cust_id,
                "name" => $_name,
                "mobile" => $_mob
            ]
        ];
        $this->accounts->$account_number = $_acc_num;
        $json = json_encode($this->accounts);
        file_put_contents($this->account_json_path, $json);
        $this->loadAccountDetails();
        echo "\nAccount created!";
        $this->displayAccountByAccountNumber($account_number);

    }

    /* 
    *Gets the customer name, account type, customer ID and mobile number as arguments and stores it using store() according to some conditions.

    *The balance will be initially zero

    *If the name is passed as a NULL value, the user should input the desired name for the account.

    *If the name is passed as a string value, it will be stored as it is.

    *The type will be passed as a string value.
        *If it is "Savings", the type will be stored as "Current".
        *If it is "Current", the type will be stored as "Savings".

    *A new 5 digit account number will be generated and the same will be checked if it is already available in the data of accounts using checkAccountNumber().
        *If it is available, the generation of account number will be done again and checked again.
        *If it is not available, the generated account number will be stored.

    @param NULL (or) string $_name
    @param NULL (or) string $_type
    @param int $_customer_id
    @param int $_mob
    */

    private function input($_name, $_type, $_customer_id, $_mob)
    {

        if ($_name === NULL) {
            echo "\n";
            $input_name = readline("Enter your name: ");
        } else {
            $input_name = $_name;
        }
        if ($_type === NULL) {
            echo "\n";
            $input_type = readline("Enter the type of account: ");
        } else {
            if ($_type == "Savings") {
                $input_type = "Current";
            } elseif ($_type == "Current") {
                $input_type = "Savings";
            }
        }
        a:
        $new_account_number = random_int(100000000, 999999999);
        if ($this->checkAccountNumber($new_account_number)) {
            goto a;
        } else {
            $this->store($new_account_number, $input_type, 0.0, $_customer_id, $input_name, $_mob);
        }

    }

    /* 
    *Retrieves the customer name, account type and customer ID of an account linked with the mobile number and returns it as an array

    @param int $_mobile

    @return array $customer_details
    */

    private function getDetailsByMobileNumber($_mobile)
    {

        foreach ($this->accounts as $account => $details) {

            if ($details->customer->mobile == $_mobile) {
                $customer_details = ["name" => $details->customer->name, "type" => $details->account->type, "customer_id" => $details->account->cust_id];
                return $customer_details;
            }

        }

    }

    /* 
     *Displays all the accounts available in the data of accounts.
     */

    private function displayAllAccounts()
    {

        foreach ($this->accounts as $account => $details) {

            echo "\nAccount number: " . $details->account->num . "\tAccount type: " . $details->account->type . "\nCustomer ID: " . $details->account->cust_id . "\nAccount Balance: " . $details->account->bal;
            echo "\nAccount holder name: " . $details->customer->name . "\nMobile number: " . $details->customer->mobile . "\n\n";

        }

    }

    /* 
    *Displays the accounts that are linked to a particular mobile number.

    @param int $_int
    */

    private function displayAccountByNumber($_mob)
    {

        foreach ($this->accounts as $account => $details) {

            if ($details->customer->mobile == $_mob) {

                echo "\nAccount number: " . $details->account->num . "\tAccount type: " . $details->account->type . "\nCustomer ID: " . $details->account->cust_id . "\nAccount Balance: " . $details->account->bal;
                echo "\nAccount holder name: " . $details->customer->name . "\nMobile number: " . $details->customer->mobile . "\n\n";
            }

        }

    }

    /* 
    *Displays the accounts that are linked to an account number.

    @param int $_acc_num
    */

    private function displayAccountByAccountNumber($_acc_num)
    {

        foreach ($this->accounts as $account => $details) {

            if ($account == $_acc_num) {

                echo "\nAccount number: " . $details->account->num . "\tAccount type: " . $details->account->type . "\nCustomer ID: " . $details->account->cust_id . "\nAccount Balance: " . $details->account->bal;
                echo "\nAccount holder name: " . $details->customer->name . "\nMobile number: " . $details->customer->mobile . "\n\n";
            }

        }

    }

    /* 
     *Initiates the creation of a new account by getting the mobile number from the user and checking if the mobile number is already linked with any account using checkMobile().
     *If it is not linked with any account, input() will be called by passing name and type as NULL along wth the mobile number and a newly generated customer ID.
     *If it is linked with one account, another account will be created but of a different type as the existing account by passing type as a string.
     *If it is linked with two accounts, no accounts will be added.
     */

    private function addNewAccount()
    {

        $mob = (int) readline("Enter your mobile number: ");
        $verify_mobile = $this->checkMobile($mob);
        if ($verify_mobile == 1) {
            echo "\nThis user already exists!\n";
            $this->displayAccountByNumber($mob);
            echo "\nHowever you still can add another account of different type\n";
            $details = $this->getDetailsByMobileNumber($mob);
            $this->input($details['name'], $details['type'], $details['customer_id'], $mob);
        } elseif ($verify_mobile == 2) {
            echo "\nThis user already exists!\n";
            $this->displayAccountByNumber($mob);
        } elseif ($verify_mobile == 0) {
            gen_cust_id:
            $new_customer_id = random_int(1000, 9999);
            if ($this->checkCustomerId($new_customer_id, $mob) == true) {
                goto gen_cust_id;
            } else {
                $this->input(NULL, NULL, $new_customer_id, $mob);
            }
        }

    }

    /* 
    *Checks if the provided amount can be deposited in the account number given.

    *Checks if the given account number already has transactions registered.
        *If there are transactions registered, the amount of each transaction will be retrieved and stored in $total_amount and this amount will be used for validation.
            *The details of the account are retrieved using the account number given.
                *If the type is Savings, $total_amount should not exceed 50000.
                    *If it does not exceed, the transaction made after adding the amount should not exceed 50000.
                        *If all the conditions are cleared, 1 is returned.
                        *Else 2 is returned.
                *If the type is Current, $total_amount should not exceed 100000.
                    *If it does not exceed, the transaction made after adding the amount should not exceed 100000.
                        *If all the conditions are cleared, 1 is returned.
                        *Else 2 is returned.
        *If there are no transactions registered, 1 is returned.

    @param int $_acc_num
    @param int $_amount

    @return int
    */

    private function checkTransactions($_acc_num)
    {

        $this->loadTransactions();
        foreach ($this->transactions as $id => $transaction) {
            if ($transaction->account_number == $_acc_num) {
                return false;
                break;
            }
        }
        return true;

    }

    private function canDeposit($_account_type, $_amount)
    {
        if ($_account_type === "Savings") {
            if ($_amount <= $this->saving_limit) {

                return true;
            } else {
                return false;
            }

        } elseif ($_account_type === "Current") {
            if ($_amount <= $this->current_limit) {

                return true;
            } else {
                return false;
            }

        }
    }

    private function getAccountType($_acc_num)
    {

        foreach ($this->accounts as $id => $account) {
            return $account->account->type;
        }

    }

    private function checkDailyLimit($_acc_num, $_amount)
    {

        $this->loadTransactions();
        $total_amount = 0.0;
        foreach ($this->transactions as $id => $transaction) {
            if ($transaction->account_number == $_acc_num) {
                $total_amount += $transaction->amount;
            }
        }
        $this->total_amount = $total_amount;
        $account_type = $this->getAccountType($_acc_num);
        if ($this->canDeposit($account_type, $total_amount)) {
            return true;
        }

    }


    /* 
    *Retrieves the balance of an account, from the data of accounts, using the account number.

    @param int $_acc_num

    @return float $details->account->bal
    */

    private function getBalance($_acc_num)
    {

        foreach ($this->accounts as $account => $details) {
            if ($account == $_acc_num) {
                return $details->account->bal;
            }
        }

    }

    /* 
    *Updates the balance of an account after depositing money using the account number.

    *The amount passed as argument is saved as the new balance for the account corresponding to the account number.

    *after updating the data, the JSON file is loaded once again so that the services can be done on the updated data of accounts.

    @param int $_acc_num
    @param float $_amount
    */

    private function updateBalance($_acc_num, $_amount)
    {

        foreach ($this->accounts as $account => $details) {
            if ($account == $_acc_num) {
                $details->account->bal = $_amount;
                $json = json_encode($this->accounts);
                file_put_contents($this->account_json_path, $json);
                $this->loadAccountDetails();
                break;
            }
        }

    }

    /* 
     *Deposits an amount in an account by getting the account number from the user and a few validation checks.
     *If the account number is in the data of accounts using checkAccountNumber(),
     *Details of the account will be displayed using displayAccountByAccountNumber() and the user should input the amount to be deposited. The amount will be validated using checkEligible().
     *If checkEligible returns 1, the existing balance of the account will be retrieved using getBalance().
     *Now the amount will be added to the existing balance and the updated balance will be stored using updateBalance().
     *A random 5 character alpha-numerical string is generated using Randomizer() and it is stored in $transaction_id. This is used as the key for the transaction to be stored in Transactions.json file.
     *Once it is stored, the Transactions.json is loaded again to retrieve the updated file for future services.
     */

    private function deposit()
    {

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
                $transaction_id = $randomizer->getBytesFromString($chars, 5);
                $this->transactions->$transaction_id = [
                    "account_number" => $input_account_number,
                    "amount" => $input_amount,
                    "date" => date('d-m-Y')
                ];
                $json = json_encode($this->transactions);
                file_put_contents($this->transaction_json_path, $json);
                $this->loadTransactions();
            } else {
                $daily_limit = $this->checkDailyLimit($input_account_number, $input_amount);
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


        }

    }

    private function closeAccount()
    {

        $input_account_number = (int) readline("Enter the account number to be removed: ");
        if ($this->checkAccountNumber($input_account_number)) {

            $this->displayAccountByAccountNumber($input_account_number);
            unset($this->accounts->$input_account_number);
            $json = json_encode($this->accounts);
            file_put_contents($this->account_json_path, $json);
            $this->loadAccountDetails();
            echo "\n\nAccount Deleted!\n";

        } else {
            echo "\nThis account number does not exists!\n";
        }

    }

    public function run()
    {

        $this->loadAccountDetails();
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