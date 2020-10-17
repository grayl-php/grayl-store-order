<?php

   namespace Grayl\Store\Order\Entity;

   use Grayl\Date\Controller\DateController;

   /**
    * Class OrderData
    * The entity for order data
    *
    * @package Grayl\Store\Order
    */
   class OrderData
   {

      /**
       * The creation date of the order
       *
       * @var DateController
       */
      private DateController $created;

      /**
       * The order id (internal reference ID)
       *
       * @var string
       */
      private string $order_id;

      /**
       * The total amount of the order
       *
       * @var float
       */
      private float $amount;

      /**
       * The base currency for the amount
       *
       * @var string
       */
      private string $currency;

      /**
       * A description for the order
       *
       * @var ?string
       */
      private ?string $description;

      /**
       * The IP address associated with the order
       *
       * @var string
       */
      private string $ip_address;


      /**
       * The class constructor
       *
       * @param DateController $date        The DateController object to set for creation
       * @param string         $order_id    The internal transaction ID of this order
       * @param float          $amount      The total amount of the order
       * @param string         $currency    The base currency of the order
       * @param ?string        $description The description of this order
       * @param string         $ip_address  The IP address for the order
       */
      public function __construct ( DateController $date,
                                    string $order_id,
                                    float $amount,
                                    string $currency,
                                    ?string $description,
                                    string $ip_address )
      {

         // Set the class data
         $this->setCreated( $date );
         $this->setOrderID( $order_id );
         $this->setAmount( $amount );
         $this->setCurrency( $currency );
         $this->setDescription( $description );
         $this->setIPAddress( $ip_address );
      }


      /**
       * Gets the creation date
       *
       * @return DateController
       */
      public function getCreated (): DateController
      {

         // Return the DateController object
         return $this->created;
      }


      /**
       * Sets the creation date
       *
       * @param DateController $date The DateController object to set for creation
       */
      public function setCreated ( DateController $date ): void
      {

         // Set the created date
         $this->created = $date;
      }


      /**
       * Gets the order id (internal reference ID)
       *
       * @return string
       */
      public function getOrderID (): string
      {

         // Get the ID
         return $this->order_id;
      }


      /**
       * Sets the order id (internal reference ID)
       *
       * @param string $order_id The internal transaction ID of this order
       */
      public function setOrderID ( string $order_id ): void
      {

         // Set the ID
         $this->order_id = $order_id;
      }


      /**
       * Gets the total amount of the order
       *
       * @return float
       */
      public function getAmount (): float
      {

         // Get the amount
         return $this->amount;
      }


      /**
       * Sets the amount of the order
       *
       * @param float $amount The total amount of the order
       */
      public function setAmount ( float $amount ): void
      {

         // Set the amount
         $this->amount = $amount;
      }


      /**
       * Gets the currency
       *
       * @return string
       */
      public function getCurrency (): string
      {

         // Get the currency
         return $this->currency;
      }


      /**
       * Sets the currency
       *
       * @param string $currency The base currency of the order
       */
      public function setCurrency ( string $currency ): void
      {

         // Set the currency
         $this->currency = $currency;
      }


      /**
       * Gets the description
       *
       * @return ?string
       */
      public function getDescription (): ?string
      {

         // Get the description
         return $this->description;
      }


      /**
       * Sets the description
       *
       * @param ?string $description The description of this order
       */
      public function setDescription ( ?string $description ): void
      {

         // Set the description
         $this->description = $description;
      }


      /**
       * Gets the IP address
       *
       * @return string
       */
      public function getIPAddress (): string
      {

         // Return the IP address
         return $this->ip_address;
      }


      /**
       * Sets the IP address for the log
       *
       * @param string $ip_address The IP address for the order
       */
      public function setIPAddress ( string $ip_address ): void
      {

         // Set the IP address
         $this->ip_address = $ip_address;
      }

   }