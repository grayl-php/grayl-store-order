<?php

   namespace Grayl\Store\Order\Storage;

   use Grayl\Store\Order\Controller\OrderController;
   use Grayl\Store\Order\Service\OrderService;

   /**
    * Class OrderDatabaseMapper
    * The interface for finding orders in the MySQL database and turning them into objects / saving them to the database
    *
    * @package Grayl\Store\Order
    */
   class OrderDatabaseMapper
   {

      /**
       * The OrderDataDatabaseMapper instance to interact with
       *
       * @var OrderDataDatabaseMapper
       */
      private OrderDataDatabaseMapper $data_mapper;

      /**
       * The OrderItemDatabaseMapper instance to interact with
       *
       * @var OrderItemDatabaseMapper
       */
      private OrderItemDatabaseMapper $item_mapper;

      /**
       * The OrderCustomerDatabaseMapper instance to interact with
       *
       * @var OrderCustomerDatabaseMapper
       */
      private OrderCustomerDatabaseMapper $customer_mapper;

      /**
       * The OrderPaymentDatabaseMapper instance to interact with
       *
       * @var OrderPaymentDatabaseMapper
       */
      private OrderPaymentDatabaseMapper $payment_mapper;


      /**
       * The class constructor
       *
       * @param OrderDataDatabaseMapper     $data_mapper     The OrderDataDatabaseMapper instance to interact with
       * @param OrderItemDatabaseMapper     $item_mapper     The OrderItemDatabaseMapper instance to interact with
       * @param OrderCustomerDatabaseMapper $customer_mapper The OrderCustomerDatabaseMapper instance to interact with
       * @param OrderPaymentDatabaseMapper  $payment_mapper  The OrderPaymentDatabaseMapper instance to interact with
       */
      public function __construct ( OrderDataDatabaseMapper $data_mapper,
                                    OrderItemDatabaseMapper $item_mapper,
                                    OrderCustomerDatabaseMapper $customer_mapper,
                                    OrderPaymentDatabaseMapper $payment_mapper )
      {

         // Set the class data
         $this->data_mapper     = $data_mapper;
         $this->item_mapper     = $item_mapper;
         $this->customer_mapper = $customer_mapper;
         $this->payment_mapper  = $payment_mapper;
      }


      /**
       * Gets a unique, unused order ID
       *
       * @param int $length The length of the ID to create
       *
       * @return string
       * @throws \Exception
       */
      public function newOrderID ( int $length ): string
      {

         // Generate a new ID
         $id = $this->generateOrderID( $length );

         // Keep looping until we have a unique ID not in the database already
         while ( $this->findOrderID( $id ) == true ) {
            // Generate another ID
            $id = $this->generateOrderID( $length );
         }

         // Return the unique generated order ID
         return $id;
      }


      /**
       * Generates a new order ID (internal reference ID)
       *
       * @param int $length The length of the ID to create
       *
       * @return string
       */
      private function generateOrderID ( int $length ): string
      {

         // Generate a random string
         $token = openssl_random_pseudo_bytes( $length );

         // Convert the binary data into hexadecimal representation
         $token = strtoupper( bin2hex( $token ) );

         // Trim to length
         $token = substr( $token,
                          0,
                          $length );

         // Return the generated order ID
         return $token;
      }


      /**
       * Checks the database for a specific order ID
       *
       * @param string $order_id The unique ID of the order to find
       *
       * @return bool
       * @throws \Exception
       */
      public function findOrderID ( string $order_id ): bool
      {

         // Use the mapper to perform a check
         return $this->data_mapper->findOrderID( $order_id );
      }


      /**
       * Returns a fully populated OrderController (and all sub-entities) from the database
       *
       * @param string       $order_id      The unique ID of the order to fetch
       * @param OrderService $order_service The OrderService instance to use
       *
       * @return OrderController
       * @throws \Exception
       */
      public function fetchOrderController ( string $order_id,
                                             OrderService $order_service ): OrderController
      {

         // Fetch the individual entities of this order
         $data     = $this->data_mapper->fetchOrderData( $order_id );
         $customer = $this->customer_mapper->fetchOrderCustomer( $order_id );
         $item_bag = $this->item_mapper->fetchOrderItemBag( $order_id );
         $payment  = $this->payment_mapper->fetchOrderPayment( $order_id );

         // Set the objects to a new order controller object and return it
         return new OrderController( $data,
                                     $item_bag,
                                     $customer,
                                     $payment,
                                     $order_service,
                                     $this );
      }


      /**
       * Determines whether to update or insert an OrderController entity
       *
       * @param OrderController $order The OrderController entity to save in the database
       *
       * @throws \Exception
       */
      public function saveOrderController ( OrderController $order ): void
      {

         // See if the OrderID already exists in the database
         if ( $this->findOrderID( $order->getOrderID() ) ) {
            // Update the existing record
            $this->updateOrderController( $order );
         } // Otherwise insert a new record
         else {
            // Insert a new record
            $this->insertOrderController( $order );
         }
      }


      /**
       * Inserts an OrderController and its entities into the database
       *
       * @param OrderController $order The OrderController entity to insert into the database
       *
       * @return bool
       * @throws \Exception
       */
      private function insertOrderController ( OrderController $order ): bool
      {

         // Get the order ID
         $order_id = $order->getOrderID();

         // Make sure we have an ID
         if ( empty( $order_id ) ) {
            // Order ID cannot be blank
            throw new \Exception( 'Order ID was blank.' );
         }

         // Insert the individual entities of the order
         $this->data_mapper->saveOrderData( $order->getOrderData() );
         $this->item_mapper->saveOrderItemBag( $order->getOrderItemBag() );

         // Save the OrderCustomer in the database
         if ( ! empty( $order->getOrderCustomer() ) ) {
            $this->customer_mapper->saveOrderCustomer( $order->getOrderCustomer() );
         }

         // Save the OrderPayment in the database
         if ( ! empty( $order->getOrderPayment() ) ) {
            $this->payment_mapper->saveOrderPayment( $order->getOrderPayment() );
         }

         // Return OK
         return true;
      }


      /**
       * Updates an OrderController and its entities in the database
       *
       * @param OrderController $order The OrderController entity to update in the database
       *
       * @return bool
       * @throws \Exception
       */
      private function updateOrderController ( OrderController $order ): bool
      {

         // Get the order ID
         $order_id = $order->getOrderID();

         // Make sure we have an ID
         if ( empty( $order_id ) ) {
            // Order ID cannot be blank
            throw new \Exception( 'Order ID was blank.' );
         }

         // Update the OrderCustomer in the database
         if ( ! empty( $order->getOrderCustomer() ) ) {
            $this->customer_mapper->saveOrderCustomer( $order->getOrderCustomer() );
         }

         // Save the OrderPayment in the database
         if ( ! empty( $order->getOrderPayment() ) ) {
            $this->payment_mapper->saveOrderPayment( $order->getOrderPayment() );
         }

         // Return OK
         return true;
      }

   }