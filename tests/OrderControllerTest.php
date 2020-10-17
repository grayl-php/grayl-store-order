<?php

   namespace Grayl\Test\Store\Order;

   use Grayl\Gateway\PDO\PDOPorter;
   use Grayl\Store\Order\Controller\OrderController;
   use Grayl\Store\Order\Entity\OrderCustomer;
   use Grayl\Store\Order\Entity\OrderData;
   use Grayl\Store\Order\Entity\OrderItem;
   use Grayl\Store\Order\Entity\OrderItemBag;
   use Grayl\Store\Order\Entity\OrderPayment;
   use Grayl\Store\Order\OrderPorter;
   use PHPUnit\Framework\TestCase;

   /**
    * Front-end for the Order package
    * TODO: Add in a test for a failed order payment to make sure the order paid flag updates
    *
    * @package Grayl\Store\Order
    */
   class OrderControllerTest extends TestCase
   {

      /**
       * Test setup for sandbox environment
       */
      public static function setUpBeforeClass (): void
      {

         // Change the PDO API environment to sandbox mode
         PDOPorter::getInstance()
                  ->setEnvironment( 'sandbox' );
      }


      /**
       * Tests the creation of an OrderController object
       *
       * @return OrderController
       * @throws \Exception
       */
      public function testCreateOrderController (): OrderController
      {

         // Create the test object
         $order = OrderPorter::getInstance()
                             ->newOrderController();

         // Basic order data
         $data = $order->getOrderData();
         $data->setAmount( 100.00 );
         $data->setDescription( 'Test order' );

         // Check the type of object created
         $this->assertInstanceOf( OrderController::class,
                                  $order );

         // Return it
         return $order;
      }


      /**
       * Tests the creation of an OrderCustomer object
       *
       * @param OrderController $order The parent order controller
       *
       * @return OrderController
       * @depends testCreateOrderController
       */
      public function testCreateOrderItems ( OrderController $order ): OrderController
      {

         // Create an array of items
         $items = [];

         // First item
         $items[] = OrderPorter::getInstance()
                               ->newOrderItem( $order->getOrderID(),
                                               'item1',
                                               'Test Item',
                                               '2',
                                               100.00 );

         // Second item
         $items[] = OrderPorter::getInstance()
                               ->newOrderItem( $order->getOrderID(),
                                               'item2',
                                               'Test Item 2',
                                               '1',
                                               50.00 );

         // Check both items created
         $this->assertInstanceOf( OrderItem::class,
                                  $items[ 0 ] );
         $this->assertInstanceOf( OrderItem::class,
                                  $items[ 1 ] );

         // Place the items into the order
         $order->putOrderItem( $items[ 0 ] );
         $order->putOrderItem( $items[ 1 ] );

         // Return the modified order with new items
         return $order;
      }


      /**
       * Tests an OrderItemBag entity
       *
       * @param OrderController $order An OrderController entity to use for testing
       *
       * @depends testCreateOrderItems
       */
      public function testOrderItemBagData ( OrderController $order ): void
      {

         // Grab the item bag
         $items = $order->getOrderItemBag();

         // Check the type of object returned
         $this->assertInstanceOf( OrderItemBag::class,
                                  $items );

         // Get the array of items from the bag
         $set = $items->getOrderItems();

         // Check the number of items
         $this->assertEquals( 2,
                              count( $set ) );

         // Perform some checks on the data
         $this->assertInstanceOf( OrderItem::class,
                                  $set[ 0 ] );
         $this->assertEquals( 'item1',
                              $set[ 0 ]->getSKU() );
         $this->assertEquals( 50.00,
                              $set[ 1 ]->getPrice() );
      }


      /**
       * Tests an OrderData entity
       *
       * @param OrderController $order An OrderController entity to use for testing
       *
       * @depends testCreateOrderItems
       */
      public function testOrderDataData ( OrderController $order ): void
      {

         // Grab the order data entity
         $data = $order->getOrderData();

         // Check the type of object created
         $this->assertInstanceOf( OrderData::class,
                                  $data );

         // Perform some checks on the data
         $this->assertEquals( 250.00,
                              $data->getAmount() );
         $this->assertEquals( 'Test order',
                              $data->getDescription() );
         $this->assertNotNull( $data->getOrderID() );
      }


      /**
       * Tests the creation of an OrderCustomer object
       *
       * @param OrderController $order The parent order controller
       *
       * @return OrderController
       * @depends testCreateOrderItems
       */
      public function testCreateOrderCustomer ( OrderController $order ): OrderController
      {

         // Create the OrderCustomer
         $customer = OrderPorter::getInstance()
                                ->newOrderCustomer( $order->getOrderID(),
                                                    'Jim',
                                                    'Doe',
                                                    'jimdoe@fake.com',
                                                    '1234 Fake Rd.',
                                                    '#3307',
                                                    'Las Vegas',
                                                    'NV',
                                                    '89129',
                                                    'US',
                                                    null );

         // Check the type of object created
         $this->assertInstanceOf( OrderCustomer::class,
                                  $customer );

         // Set the OrderCustomer
         $order->setOrderCustomer( $customer );

         // Return the modified order with the customer added
         return $order;
      }


      /**
       * Tests an OrderCustomer entity
       *
       * @param OrderController $order An OrderController entity to use for testing
       *
       * @depends testCreateOrderCustomer
       */
      public function testOrderCustomerData ( OrderController $order ): void
      {

         // Grab the customer entity
         $customer = $order->getOrderCustomer();

         // Check the type of object created
         $this->assertInstanceOf( OrderCustomer::class,
                                  $customer );

         // Perform some checks on the data
         $this->assertEquals( 'Jim',
                              $customer->getFirstName() );
         $this->assertEquals( '89129',
                              $customer->getPostcode() );
      }


      /**
       * Tests the creation of an OrderPayment object
       *
       * @param OrderController $order The parent order controller
       *
       * @return OrderController
       * @depends testCreateOrderCustomer
       * @throws \Exception
       */
      public function testCreateOrderPayment ( OrderController $order ): OrderController
      {

         // First make sure the order is unpaid
         $this->assertFalse( $order->isOrderPaid() );

         // Create the OrderPayment
         $payment = OrderPorter::getInstance()
                               ->newOrderPayment( $order->getOrderID(),
                                                  'test',
                                                  'test',
                                                  250.00,
                                                  'capture',
                                                  true,
                                                  'test payment' );

         // Check the type of object created
         $this->assertInstanceOf( OrderPayment::class,
                                  $payment );

         // Set the OrderPayment
         $order->setOrderPayment( $payment );

         // Return the modified order with the payment added
         return $order;
      }


      /**
       * Tests an OrderPayment entity
       *
       * @param OrderController $order An OrderController entity to use for testing
       *
       * @depends testCreateOrderPayment
       */
      public function testOrderPaymentData ( OrderController $order ): void
      {

         // First make sure the order is now paid
         $this->assertTrue( $order->isOrderPaid() );

         // Grab the payment entity
         $payment = $order->getOrderPayment();

         // Check the type of object created
         $this->assertInstanceOf( OrderPayment::class,
                                  $payment );

         // Perform some checks on the data
         $this->assertEquals( 'test',
                              $payment->getProcessor() );
         $this->assertEquals( 'capture',
                              $payment->getAction() );
         $this->assertTrue( (boolean) $payment->isSuccessful() );
         $this->assertEquals( 250.00,
                              $payment->getAmount() );
         $this->assertEquals( 'test payment',
                              $payment->getMetadata() );
      }


      /**
       * Tests saving an OrderController to the database
       *
       * @param OrderController $original_order An OrderController entity to use for testing
       *
       * @throws \Exception
       * @depends testCreateOrderPayment
       */
      public function testFetchOrderControllerFromDatabase ( OrderController $original_order ): void
      {

         // Save the original OrderController into the database
         $original_order->saveOrder();

         // Request the saved OrderController from the database
         $fetched_order = OrderPorter::getInstance()
                                     ->fetchOrderController( $original_order->getOrderID() );

         // Check the type of object returned
         $this->assertInstanceOf( OrderController::class,
                                  $fetched_order );

         // Now rerun the original tests on the fetched OrderController to make sure it matches up
         $this->testOrderDataData( $fetched_order );
         $this->testOrderItemBagData( $fetched_order );
         $this->testOrderCustomerData( $fetched_order );
         $this->testOrderPaymentData( $fetched_order );
      }

   }
