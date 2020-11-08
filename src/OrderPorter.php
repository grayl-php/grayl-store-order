<?php

   namespace Grayl\Store\Order;

   use Grayl\Config\ConfigPorter;
   use Grayl\Database\Main\DatabasePorter;
   use Grayl\Date\DatePorter;
   use Grayl\Mixin\Common\Traits\StaticTrait;
   use Grayl\Store\Order\Config\OrderConfig;
   use Grayl\Store\Order\Controller\OrderController;
   use Grayl\Store\Order\Entity\OrderCustomer;
   use Grayl\Store\Order\Entity\OrderData;
   use Grayl\Store\Order\Entity\OrderItem;
   use Grayl\Store\Order\Entity\OrderItemBag;
   use Grayl\Store\Order\Entity\OrderPayment;
   use Grayl\Store\Order\Service\OrderService;
   use Grayl\Store\Order\Storage\OrderCustomerDatabaseMapper;
   use Grayl\Store\Order\Storage\OrderDatabaseMapper;
   use Grayl\Store\Order\Storage\OrderDataDatabaseMapper;
   use Grayl\Store\Order\Storage\OrderItemDatabaseMapper;
   use Grayl\Store\Order\Storage\OrderPaymentDatabaseMapper;

   /**
    * Front-end for the Order package
    *
    * @package Grayl\Store\Order
    */
   class OrderPorter
   {

      // Use the static instance trait
      use StaticTrait;

      /**
       * The name of the config file for the package
       *
       * @var string
       */
      private string $config_file = 'store-order.php';

      /**
       * The config instance for the package
       *
       * @var OrderConfig
       */
      private OrderConfig $config;


      /**
       * The class constructor
       *
       * @throws \Exception
       */
      public function __construct ()
      {

         // Create the config instance from the config file
         /** @var OrderConfig $config */
         $config = ConfigPorter::getInstance()
                               ->includeConfigFile( $this->config_file );

         // Set the config for the class
         $this->config = $config;
      }


      /**
       * Creates a new OrderController
       *
       * @param int $id_length The length of the ID to create
       *
       * @return OrderController
       * @throws \Exception
       */
      public function newOrderController ( int $id_length = 13 ): OrderController
      {

         // Create an order database mapper
         $order_mapper = $this->newOrderDatabaseMapper();

         // Get a new order ID
         $order_id = $order_mapper->newOrderID( $id_length );

         // Create the new OrderData entity
         $order_data = new OrderData( DatePorter::getInstance()
                                                ->newDateController( null ),
                                      $order_id,
                                      0,
                                      'USD',
                                      null,
                                      $_SERVER[ 'REMOTE_ADDR' ] );

         // Return the entity
         return new OrderController( $order_data,
                                     new OrderItemBag(),
                                     null,
                                     null,
                                     $this->newOrderService(),
                                     $order_mapper );
      }


      /**
       * Gets a saved OrderController and its entities from the database
       *
       * @param string $order_id The unique ID of the order to fetch
       *
       * @return OrderController
       * @throws \Exception
       */
      public function fetchOrderController ( string $order_id ): OrderController
      {

         // Create an order database mapper
         $order_mapper = $this->newOrderDatabaseMapper();

         // Request the order
         return $order_mapper->fetchOrderController( $order_id,
                                                     $this->newOrderService() );
      }


      /**
       * Creates a new OrderItem entity with the supplied values
       *
       * @param string $order_id The internal transaction ID of the parent order
       * @param string $sku      The unique item SKU (no spaces or special characters)
       * @param string $name     The name of the item
       * @param int    $quantity The item quantity
       * @param float  $price    The price of the item
       *
       * @return OrderItem
       */
      public function newOrderItem ( string $order_id,
                                     string $sku,
                                     string $name,
                                     int $quantity,
                                     float $price ): OrderItem
      {

         // Return a new OrderItem entity
         return new OrderItem( $order_id,
                               $sku,
                               $name,
                               $quantity,
                               $price );
      }


      /**
       * Creates a new OrderCustomer entity with the supplied values
       *
       * @param string  $order_id      The internal transaction ID of the parent order
       * @param string  $first_name    The first name of the customer
       * @param string  $last_name     The last name of the customer
       * @param string  $email_address The email address of the customer
       * @param string  $address_1     The address part 1
       * @param ?string $address_2     The address part 2
       * @param string  $city          The city
       * @param string  $state         The state
       * @param string  $postcode      The postcode
       * @param string  $country       The country
       * @param ?string $phone_number  The phone number
       *
       * @return OrderCustomer
       */
      public function newOrderCustomer ( string $order_id,
                                         string $first_name,
                                         string $last_name,
                                         string $email_address,
                                         string $address_1,
                                         ?string $address_2,
                                         string $city,
                                         string $state,
                                         string $postcode,
                                         string $country,
                                         ?string $phone_number ): OrderCustomer
      {

         // Return a new OrderCustomer entity
         return new OrderCustomer( $order_id,
                                   $first_name,
                                   $last_name,
                                   $email_address,
                                   $address_1,
                                   $address_2,
                                   $city,
                                   $state,
                                   $postcode,
                                   $country,
                                   $phone_number );
      }


      /**
       * Creates a new OrderPayment entity with the supplied values
       *
       * @param string  $order_id     The internal transaction ID of the associated order
       * @param string  $reference_id The external reference ID of this payment
       * @param string  $processor    The processor of this payment
       * @param float   $amount       The amount to charge
       * @param string  $action       The action of the payment (authorize, capture, etc.)
       * @param bool    $successful   If the payment action was successful
       * @param ?string $metadata     The metadata to for the payment
       *
       * @return OrderPayment
       * @throws \Exception
       */
      public function newOrderPayment ( string $order_id,
                                        string $reference_id,
                                        string $processor,
                                        float $amount,
                                        string $action,
                                        bool $successful,
                                        ?string $metadata ): OrderPayment
      {

         // Return a new OrderPayment entity
         return new OrderPayment( DatePorter::getInstance()
                                            ->newDateController( null ),
                                  $order_id,
                                  $reference_id,
                                  $processor,
                                  $amount,
                                  $action,
                                  $successful,
                                  $metadata );
      }


      /**
       * Creates a new OrderDatabaseMapper
       *
       * @return OrderDatabaseMapper
       * @throws \Exception
       */
      private function newOrderDatabaseMapper (): OrderDatabaseMapper
      {

         // Get the database docker
         $database_docker = DatabasePorter::getInstance();

         // Create an order database mapper
         return new OrderDatabaseMapper( new OrderDataDatabaseMapper( 'store_order',
                                                                      $database_docker,
                                                                      DatePorter::getInstance() ),
                                         new OrderItemDatabaseMapper( 'store_order_item',
                                                                      $database_docker ),
                                         new OrderCustomerDatabaseMapper( 'store_order_customer',
                                                                          $database_docker ),
                                         new OrderPaymentDatabaseMapper( 'store_order_payment',
                                                                         $database_docker,
                                                                         DatePorter::getInstance() ) );
      }


      /**
       * Creates a new OrderService
       *
       * @return OrderService
       * @throws \Exception
       */
      private function newOrderService (): OrderService
      {

         // Create the service
         return new OrderService( $this->config->getFailedPaymentActions(),
                                  $this->config->getPendingPaymentActions(),
                                  $this->config->getCompletedPaymentActions() );
      }

   }