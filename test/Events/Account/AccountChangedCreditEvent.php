<?php
/**
 * Created by PhpStorm.
 * User: laptop
 * Date: 11/10/17
 * Time: 08:32 م
 */

namespace Test\Events\Account;


use JPuminate\Contracts\EventBus\Events\Event;

class AccountChangedCreditEvent extends Event
{
    public $account_id;
    public $costumer_id;

    public function __construct($account_id, $costumer_id, $sender = null, $pusher_id = null)
    {
        parent::__construct($sender, $pusher_id);
        $this->account_id = $account_id;
        $this->costumer_id = $costumer_id;
    }
}