<?php

use App\Models\Concert;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Billing\FakePaymentGateway;
use App\Billing\PaymentFailedException;

class FakePaymentGatewayTest extends TestCase
{
    use DatabaseMigrations;

    public function testChargesWithAValidPaymentTokenAreSuccessful()
    {
        $paymentGateway = new FakePaymentGateway;

        $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());

        $this->assertEquals(2500, $paymentGateway->totalCharges());
    }

    public function testChargesWithAInvalidPaymentTokenAreSuccessful()
    {
        try {
            $paymentGateway = new FakePaymentGateway;

            $paymentGateway->charge(2500, 'ivalid-token');
        } catch (PaymentFailedException $e) {
            return $e;
        }

        $this->fail();
    }

}
