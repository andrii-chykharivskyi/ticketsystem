<?php

use App\Models\Concert;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Exceptions\NotEnoughTicketsException;

class OrderTest extends TestCase
{
    use DatabaseMigrations;

    /*private function orderTickets($params, $specified_concert = null)
    {
        if ($specified_concert){
            $concert = $specified_concert;
        } else {
            $concert = factory(Concert::class)->states('published')->create();
        }

        $this->json('POST', "/concerts/{$concert->id}/orders", $params);
    }*/

    public function testTicketsAreReleasedWhenOrderIsCancelled()
    {
        $concert = factory(Concert::class)->create();
        $concert->addTickets(10);
        $order = $concert->orderTickets('andy@example.com' , 5);

        $this->assertEquals(5, $concert->ticketsRemaining());

        $order->cancel();

        $this->assertEquals(10, $concert->ticketsRemaining());
        $this->assertNull(Order::find($order->id));
    }

    public function testConvertingToArray()
    {
        $concert = factory(Concert::class)->create(['ticket_price' => 1200,]);
        $concert->addTickets(5);
        $order = $concert->orderTickets('andy@example.com' , 5);

        $result = $order->toArray();

        $this->assertEquals([
            'email' => 'andy@example.com',
            'ticket_quantity' => 5,
            'amount' => 6000,
        ], $result);
    }
}
