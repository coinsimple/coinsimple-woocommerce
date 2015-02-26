<?php
require "vendor/autoload.php";

class InvoiceTest extends \Codeception\TestCase\Test
{
    use Codeception\Specify;

    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    public function testConstructorParameter()
    {
        $this->specify('when passing a number', function() {
            $i = new \CoinSimple\Invoice(5);
        }, ['throws' => 'InvalidArgumentException']);

        $this->specify('when passing a string', function() {
            $i = new \CoinSimple\Invoice('str');
        }, ['throws' => 'InvalidArgumentException']);

        $this->specify('when passing an array', function() {
            $i = new \CoinSimple\Invoice(array());
            verify($i->data())->equals(array());
        });

        $this->specify('when passing nothing', function() {
            $i = new \CoinSimple\Invoice(array());
            verify($i->data())->equals(array());
        });
    }

    public function testFullInvoice() {
        $data = array(
            "name" => "Gabriel",
            "email" => "gmanricks@gmail.com",
            "items" => array(
                array("description" => "item", "price" => 3.0, "quantity" => 6)
            ),
            "processor" => "coinsimple",
            "rate" => "coinsimple",
            "currency" => "usd",
            "notes" => "Note",
            "percent" => 1.5,
            "custom" => array("id" => 4),
            "callback_url" => "http://coinsimple.com/callback",
            "redirect_url" => "http://coinsimple.com/redirect",
            "invoice_type" => "date",
            "interval" => 5,
            "recurring_times" => 2
        );

        $this->specify('from constructor', function() use($data) {
            $i = new \CoinSimple\Invoice($data);
            verify($i->data())->equals($data);
        });

        $this->specify('from methods', function() use($data) {
            $i = new \CoinSimple\Invoice();
            $i->setName("Gabriel")
              ->setEmail("gmanricks@gmail.com")
              ->addItem(array("description" => "item", "price" => 3.0, "quantity" => 6))
              ->setProcessor("coinsimple")
              ->setRate("coinsimple")
              ->setCurrency("usd")
              ->setNotes("Note")
              ->setPercent(1.5)
              ->setCustom(array("id" => 4))
              ->setCallbackUrl("http://coinsimple.com/callback")
              ->setRedirectUrl("http://coinsimple.com/redirect")
              ->recurBydate(5)
              ->stopAfter(2);
            verify($i->data())->equals($data);
        });
    }

    public function testSetName() {
        $this->specify('sets name as a string', function() {
            $i = new \CoinSimple\Invoice();
            verify($i->data())->equals(array());

            $i->setName("Gabriel");
            $data = $i->data();
            verify(array_key_exists('name', $data))->true();
            verify($data['name'])->equals("Gabriel");

            $i->setName(5);
            verify($i->data()['name'])->equals("5");
        });
    }

    public function testSetEmail() {
        $this->specify('checks for valid email address', function() {
            $i = new \CoinSimple\Invoice();
            verify($i->data())->equals(array());

            $i->setEmail("gmanricks@gmail.com");
            $data = $i->data();
            verify(array_key_exists('email', $data))->true();
            verify($data['email'])->equals("gmanricks@gmail.com");
        });

        $this->specify('when passing an invalid email', function() {
            $i = new \CoinSimple\Invoice();
            $i->setEmail("gmanricks.com");
        }, ['throws' => 'InvalidArgumentException']);
    }

    public function testAddItem() {
        $this->specify('when passing an invalid item', function() {
            $i = new \CoinSimple\Invoice();
            $i->addItem('invalid item');
        }, ['throws' => 'InvalidArgumentException']);


        $this->specify('when passing an incomplete item', function() {
            $i = new \CoinSimple\Invoice();
            $i->addItem(array('price' => 24, 'quantity' => 2));
        }, ['throws' => 'InvalidArgumentException']);

        $this->specify('when passing a valid item', function() {
            $i = new \CoinSimple\Invoice();
            verify($i->data())->equals(array());

            $i->addItem(array('price' => 24, 'quantity' => 2, 'description' => 'item'));

            $data = $i->data();
            verify(array_key_exists('items', $data))->true();
            verify($data['items'])->equals(array(
                array('price' => 24, 'quantity' => 2, 'description' => 'item')
            ));
        });
    }

    public function testSetProcessor() {
        $this->specify('sets the processor', function() {
            $i = new \CoinSimple\Invoice();
            verify($i->data())->equals(array());

            $i->setProcessor('coinsimple');
            $data = $i->data();
            verify(array_key_exists('processor', $data))->true();
            verify($data['processor'])->equals("coinsimple");
        });

        $this->specify('converts the processor to lowercase', function() {
            $i = new \CoinSimple\Invoice();
            verify($i->data())->equals(array());

            $i->setProcessor('CoinSimple');
            $data = $i->data();
            verify(array_key_exists('processor', $data))->true();
            verify($data['processor'])->equals("coinsimple");
        });
    }

    public function testSetRate() {
        $this->specify('sets the rate', function() {
            $i = new \CoinSimple\Invoice();
            verify($i->data())->equals(array());

            $i->setRate('coinsimple');
            $data = $i->data();
            verify(array_key_exists('rate', $data))->true();
            verify($data['rate'])->equals("coinsimple");
        });

        $this->specify('converts the rate to lowercase', function() {
            $i = new \CoinSimple\Invoice();
            verify($i->data())->equals(array());

            $i->setRate('CoinSimple');
            $data = $i->data();
            verify(array_key_exists('rate', $data))->true();
            verify($data['rate'])->equals("coinsimple");
        });
    }

    public function testSetCurrency() {
        $this->specify('sets the currency', function() {
            $i = new \CoinSimple\Invoice();
            verify($i->data())->equals(array());

            $i->setCurrency('coinsimple');
            $data = $i->data();
            verify(array_key_exists('currency', $data))->true();
            verify($data['currency'])->equals("coinsimple");
        });

        $this->specify('converts the currency to lowercase', function() {
            $i = new \CoinSimple\Invoice();
            verify($i->data())->equals(array());

            $i->setCurrency('CoinSimple');
            $data = $i->data();
            verify(array_key_exists('currency', $data))->true();
            verify($data['currency'])->equals("coinsimple");
        });
    }

    public function testSetNotes() {
        $this->specify('sets the notes fields', function() {
            $i = new \CoinSimple\Invoice();
            verify($i->data())->equals(array());

            $i->setNotes('CoinSimple Memo');
            $data = $i->data();
            verify(array_key_exists('notes', $data))->true();
            verify($data['notes'])->equals("CoinSimple Memo");
        });
    }

    public function testSetPercent() {
        $this->specify('sets the percent field', function() {
            $i = new \CoinSimple\Invoice();
            verify($i->data())->equals(array());

            $i->setPercent(4.5);
            $data = $i->data();
            verify(array_key_exists('percent', $data))->true();
            verify($data['percent'])->equals(4.5);
        });

        $this->specify('converts strings to float', function() {
            $i = new \CoinSimple\Invoice();
            verify($i->data())->equals(array());

            $i->setPercent("4.5");
            $data = $i->data();
            verify(array_key_exists('percent', $data))->true();
            verify($data['percent'])->equals(4.5);
        });
    }

    public function testSetDiscount() {
        $this->specify('sets the percent field', function() {
            $i = new \CoinSimple\Invoice();
            verify($i->data())->equals(array());

            $i->setDiscount(4.5);
            $data = $i->data();
            verify(array_key_exists('percent', $data))->true();
            verify($data['percent'])->equals(4.5);
        });

        $this->specify('converts strings to float', function() {
            $i = new \CoinSimple\Invoice();
            verify($i->data())->equals(array());

            $i->setDiscount("4.5");
            $data = $i->data();
            verify(array_key_exists('percent', $data))->true();
            verify($data['percent'])->equals(4.5);
        });
    }

    public function testSetCustom() {
        $this->specify('sets the custom field', function() {
            $i = new \CoinSimple\Invoice();
            verify($i->data())->equals(array());

            $i->setCustom("custom data");
            $data = $i->data();
            verify(array_key_exists('custom', $data))->true();
            verify($data['custom'])->equals("custom data");
        });

        $this->specify('converts non arrays to strings', function() {
            $i = new \CoinSimple\Invoice();
            verify($i->data())->equals(array());

            $i->setCustom(34);
            $data = $i->data();
            verify(array_key_exists('custom', $data))->true();
            verify($data['custom'])->equals("34");
        });

        $this->specify('does not convert arrays or hashes', function() {
            $i = new \CoinSimple\Invoice();
            verify($i->data())->equals(array());

            $i->setCustom(array(3, 5, 6));
            $data = $i->data();
            verify(array_key_exists('custom', $data))->true();
            verify($data['custom'])->equals(array(3, 5, 6));

            $i->setCustom(array("id" => 23));
            $data = $i->data();
            verify(array_key_exists('custom', $data))->true();
            verify($data['custom'])->equals(array("id" => 23));
        });
    }

    public function testSetCallbackUrl() {
        $this->specify('sets the callback url fields', function() {
            $i = new \CoinSimple\Invoice();
            verify($i->data())->equals(array());

            $i->setCallbackUrl('http://demo.com');
            $data = $i->data();
            verify(array_key_exists('callback_url', $data))->true();
            verify($data['callback_url'])->equals("http://demo.com");
        });

        $this->specify('converts callback url to string', function() {
            $i = new \CoinSimple\Invoice();
            verify($i->data())->equals(array());

            $i->setCallbackUrl(5);
            $data = $i->data();
            verify(array_key_exists('callback_url', $data))->true();
            verify($data['callback_url'])->equals("5");
        });
    }

    public function testSetRedirectUrl() {
        $this->specify('sets the redirect url fields', function() {
            $i = new \CoinSimple\Invoice();
            verify($i->data())->equals(array());

            $i->setRedirectUrl('http://demo.com');
            $data = $i->data();
            verify(array_key_exists('redirect_url', $data))->true();
            verify($data['redirect_url'])->equals("http://demo.com");
        });

        $this->specify('converts redirect url to string', function() {
            $i = new \CoinSimple\Invoice();
            verify($i->data())->equals(array());

            $i->setRedirectUrl(5);
            $data = $i->data();
            verify(array_key_exists('redirect_url', $data))->true();
            verify($data['redirect_url'])->equals("5");
        });
    }

    public function testSetRecurringTimes() {
        $this->specify('sets the percent field', function() {
            $i = new \CoinSimple\Invoice();
            verify($i->data())->equals(array());

            $i->setRecurringTimes(4);
            $data = $i->data();
            verify(array_key_exists('recurring_times', $data))->true();
            verify($data['recurring_times'])->equals(4);
        });

        $this->specify('converts param to int', function() {
            $i = new \CoinSimple\Invoice();
            verify($i->data())->equals(array());

            $i->setRecurringTimes("4");
            $data = $i->data();
            verify(array_key_exists('recurring_times', $data))->true();
            verify($data['recurring_times'])->equals(4);
        });
    }

    public function testRecurByDays() {
        $this->specify('sets the recurring fields', function() {
            $i = new \CoinSimple\Invoice();
            verify($i->data())->equals(array());

            $i->recurByDays(4);
            $data = $i->data();
            verify(array_key_exists('invoice_type', $data))->true();
            verify(array_key_exists('interval', $data))->true();
            verify($data['invoice_type'])->equals("days");
            verify($data['interval'])->equals(4);
        });

        $this->specify('converts param to int', function() {
            $i = new \CoinSimple\Invoice();
            verify($i->data())->equals(array());

            $i->recurByDays("4");
            $data = $i->data();
            verify(array_key_exists('invoice_type', $data))->true();
            verify(array_key_exists('interval', $data))->true();
            verify($data['invoice_type'])->equals("days");
            verify($data['interval'])->equals(4);
        });
    }

    public function testRecurByDate() {
        $this->specify('sets the recurring fields', function() {
            $i = new \CoinSimple\Invoice();
            verify($i->data())->equals(array());

            $i->recurByDate(4);
            $data = $i->data();
            verify(array_key_exists('invoice_type', $data))->true();
            verify(array_key_exists('interval', $data))->true();
            verify($data['invoice_type'])->equals("date");
            verify($data['interval'])->equals(4);
        });

        $this->specify('converts param to int', function() {
            $i = new \CoinSimple\Invoice();
            verify($i->data())->equals(array());

            $i->recurByDate("4");
            $data = $i->data();
            verify(array_key_exists('invoice_type', $data))->true();
            verify(array_key_exists('interval', $data))->true();
            verify($data['invoice_type'])->equals("date");
            verify($data['interval'])->equals(4);
        });
    }
}
