<?php

   namespace Grayl\Store\Order\Controller;

   use Grayl\Store\Order\Entity\OrderCustomer;
   use Grayl\Store\Order\Entity\OrderData;
   use Grayl\Store\Order\Entity\OrderItem;
   use Grayl\Store\Order\Entity\OrderItemBag;
   use Grayl\Store\Order\Entity\OrderPayment;
   use Grayl\Store\Order\Service\OrderService;
   use Grayl\Store\Order\Storage\OrderDatabaseMapper;

   /**
    * Class OrderController
    * The controller for working with Order entities
    *
    * @package Grayl\Display
    */
   class OrderController
   {

      /**
       * An OrderData object that holds all of the order data
       *
       * @var OrderData
       */
      private OrderData $order_data;

      /**
       * An OrderCustomer object that holds all of the customer information
       *
       * @var ?OrderCustomer
       */
      private ?OrderCustomer $order_customer;

      /**
       * An OrderItemBag object that holds all of the order items
       *
       * @var OrderItemBag
       */
      private OrderItemBag $order_item_bag;

      /**
       * An OrderPayment object that holds all of the payment information
       *
       * @var ?OrderPayment
       */
      private ?OrderPayment $order_payment;

      /**
       * The OrderService instance to interact with
       *
       * @var OrderService
       */
      private OrderService $order_service;

      /**
       * The OrderDatabaseMapper instance to interact with
       *
       * @var OrderDatabaseMapper
       */
      private OrderDatabaseMapper $database_mapper;


      /**
       * The class constructor
       *
       * @param OrderData           $order_data      The OrderData entity to control
       * @param OrderItemBag        $order_item_bag  The OrderItemBag instance to control
       * @param ?OrderCustomer      $order_customer  The OrderCustomer entity to control for customer info
       * @param ?OrderPayment       $order_payment   The OrderPayment instance to control
       * @param OrderService        $order_service   The OrderService instance to use
       * @param OrderDatabaseMapper $database_mapper The OrderDatabaseMapper instance to use
       */
      public function __construct ( OrderData $order_data,
                                    OrderItemBag $order_item_bag,
                                    ?OrderCustomer $order_customer,
                                    ?OrderPayment $order_payment,
                                    OrderService $order_service,
                                    OrderDatabaseMapper $database_mapper )
      {

         // Set the class data
         $this->order_data     = $order_data;
         $this->order_item_bag = $order_item_bag;
         $this->order_customer = $order_customer;
         $this->order_payment  = $order_payment;

         // Set the service entity
         $this->order_service = $order_service;

         // Set the database mapper
         $this->database_mapper = $database_mapper;
      }


      /**
       * Gets the OrderData object
       *
       * @returns OrderData
       */
      public function getOrderData (): OrderData
      {

         // Return the order data object
         return $this->order_data;
      }


      /**
       * Returns the unique order ID
       *
       * @returns string
       */
      public function getOrderID (): string
      {

         // Return the order ID
         return $this->order_data->getOrderID();
      }


      /**
       * Returns the order amount
       *
       * @returns float
       */
      public function getOrderAmount (): float
      {

         // Return the order amount
         return $this->order_data->getAmount();
      }


      /**
       * Gets the OrderItemBag object
       *
       * @return OrderItemBag
       */
      public function getOrderItemBag (): OrderItemBag
      {

         // Return the item bag
         return $this->order_item_bag;
      }


      /**
       * Places an OrderItem entity into the OrderItemBag
       *
       * @param OrderItem $item An OrderItem entity to add into the order
       */
      public function putOrderItem ( OrderItem $item ): void
      {

         // Save the new item into the bag
         $this->order_service->putOrderItem( $this->order_data,
                                             $this->order_item_bag,
                                             $item );
      }


      /**
       * Gets the OrderCustomer object
       *
       * @returns OrderCustomer
       */
      public function getOrderCustomer (): ?OrderCustomer
      {

         // Return the customer object
         return $this->order_customer;
      }


      /**
       * Sets an OrderCustomer in this controller
       *
       * @param OrderCustomer $order_customer An OrderCustomer entity to save
       */
      public function setOrderCustomer ( OrderCustomer $order_customer ): void
      {

         // Save the new customer into this controller
         $this->order_customer = $order_customer;
      }


      /**
       * Gets the OrderPayment object
       *
       * @returns OrderPayment
       */
      public function getOrderPayment (): ?OrderPayment
      {

         // Return the OrderPayment object
         return $this->order_payment;
      }


      /**
       * Sets an OrderPayment in this controller
       *
       * @param OrderPayment $order_payment An OrderPayment entity to save for the order
       */
      public function setOrderPayment ( OrderPayment $order_payment ): void
      {

         // Save the new payment into this controller
         $this->order_payment = $order_payment;
      }


      /**
       * Determines if an order is paid based on its current OrderPayment entity
       *
       * @returns bool
       */
      public function isOrderPaid (): bool
      {

         // Request the status from the service
         return $this->order_service->isOrderPaid( $this->order_data,
                                                   $this->order_payment );
      }


      /**
       * Determines if an order is closed from further modification
       *
       * @returns bool
       */
      public function isOrderClosed (): bool
      {

         // Return the status from the service
         return $this->order_service->isOrderClosed( $this->order_payment );
      }


      /**
       * Saves this OrderController and its sub entities into the database
       *
       * @throws \Exception
       */
      public function saveOrder (): void
      {

         // Insert this OrderController into the database or update if it already exists
         $this->database_mapper->saveOrderController( $this );
      }

   }