<?php

namespace CoinSimple;

class Invoice
{
    protected $options;

    public function __construct($opts = array())
    {
        if (!is_array($opts)) {
            throw new \InvalidArgumentException('Invoice expects a hash');
        }

        $this->options = array();
        $this->processInputHash($opts);
    }

    protected function processInputHash($opts)
    {
        foreach ($opts as $key => $val) {
            $command = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
            if (method_exists($this, $command)) {
                call_user_func(array($this, $command), $val);
            }
        }

        if (array_key_exists('interval', $opts) && array_key_exists('invoice_type', $opts)) {
            $command = 'recurBy' . ucfirst(strtolower($opts['invoice_type']));
            if (method_exists($this, $command)) {
                call_user_func(array($this, $command), $opts['interval']);
            }
        }

        if (array_key_exists('items', $opts) && is_array($opts['items'])) {
            foreach ($opts['items'] as $item) {
                $this->addItem($item);
            }
        }
    }

    public function setName($name)
    {
        $this->options['name'] = strval($name);
        return $this;
    }

    public function setEmail($email)
    {
        $pattern = '/^([^@\s]+)@((?:[-a-z0-9]+\.)+[a-z]{2,})$/';
        if (preg_match($pattern, $email) != 1) {
            throw new \InvalidArgumentException('Invoice expects a valid email');
        }
        $this->options['email'] = strval($email);
        return $this;
    }

    public function addItem($item)
    {
        if (!is_array($item)) {
            throw new \InvalidArgumentException('An Invoice item must be a Hash');
        }

        if (!array_key_exists('price', $item)
        || !array_key_exists('description', $item)
        || !array_key_exists('quantity', $item)) {
            throw new \InvalidArgumentException('An Invoice item must contain a price, description and quantity');
        }

        if (!array_key_exists('items', $this->options)) {
            $this->options['items'] = array();
        }

        array_push($this->options['items'], $item);
        return $this;
    }

    public function setProcessor($processor)
    {
        $this->options['processor'] = strtolower(strval($processor));
        return $this;
    }

    public function setRate($rate)
    {
        $this->options['rate'] = strtolower(strval($rate));
        return $this;
    }

    public function setCurrency($currency)
    {
        $this->options['currency'] = strtolower(strval($currency));
        return $this;
    }

    public function setNotes($notes)
    {
        $this->options['notes'] = strval($notes);
        return $this;
    }

    public function setPercent($percent)
    {
        $this->options['percent'] = floatval($percent);
        return $this;
    }

    public function setDiscount($percent)
    {
        return $this->setPercent($percent);
    }

    public function setCustom($data)
    {
        if (!is_array($data)) {
            $data = strval($data);
        }

        $this->options['custom'] = $data;
        return $this;
    }

    public function setCallbackUrl($url)
    {
        $this->options['callback_url'] = strval($url);
        return $this;
    }

    public function setRedirectUrl($url)
    {
        $this->options['redirect_url'] = strval($url);
        return $this;
    }

    public function setRecurringTimes($times)
    {
        $this->options['recurring_times'] = intval($times);
        return $this;
    }

    public function stopAfter($times)
    {
        return $this->setRecurringTimes($times);
    }

    public function recurByDays($numDays)
    {
        $this->options['invoice_type'] = 'days';
        $this->options['interval'] = intval($numDays);
        return $this;
    }

    public function recurByDate($dayOfMonth)
    {
        $this->options['invoice_type'] = 'date';
        $this->options['interval'] = intval($dayOfMonth);
        return $this;
    }

    public function data()
    {
        return $this->options;
    }
}
