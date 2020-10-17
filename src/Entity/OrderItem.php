<?php

   namespace Grayl\Store\Order\Entity;

   /**
    * Class OrderItem
    * The entity for a single order item
    *
    * @package Grayl\Store\Order
    */
   class OrderItem
   {

      /**
       * The order id (internal reference ID)
       *
       * @var string
       */
      private string $order_id;

      /**
       * The unique item SKU (no spaces or special characters)
       *
       * @var string
       */
      private string $sku;

      /**
       * The name of the item
       *
       * @var string
       */
      private string $name;

      /**
       * The quantity of the item
       *
       * @var int
       */
      private int $quantity;

      /**
       * The price of the item
       *
       * @var float
       */
      private float $price;


      /**
       * Class constructor
       *
       * @param string $order_id The internal transaction ID of this order
       * @param string $sku      The unique item SKU (no spaces or special characters)
       * @param string $name     The name of the item
       * @param int    $quantity The item quantity
       * @param float  $price    The price of the item
       */
      public function __construct ( string $order_id,
                                    string $sku,
                                    string $name,
                                    int $quantity,
                                    float $price )
      {

         // Set the class data
         $this->setOrderID( $order_id );
         $this->setSKU( $sku );
         $this->setName( $name );
         $this->setQuantity( $quantity );
         $this->setPrice( $price );
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
       * Gets the SKU
       *
       * @return string
       */
      public function getSKU (): string
      {

         // Return the SKU
         return $this->sku;
      }


      /**
       * Sets the SKU
       *
       * @param string $sku The unique item SKU (no spaces or special characters)
       */
      public function setSKU ( string $sku ): void
      {

         // Set the SKU
         $this->sku = $sku;
      }


      /**
       * Gets the name
       *
       * @return string
       */
      public function getName (): string
      {

         // Get the name
         return $this->name;
      }


      /**
       * Sets the name
       *
       * @param string $name The name
       */
      public function setName ( string $name ): void
      {

         // Set the name
         $this->name = $name;
      }


      /**
       * Gets the quantity
       *
       * @return int
       */
      public function getQuantity (): int
      {

         // Get the quantity
         return $this->quantity;
      }


      /**
       * Sets the quantity
       *
       * @param int $quantity The quantity
       */
      public function setQuantity ( int $quantity ): void
      {

         // Set the quantity
         $this->quantity = $quantity;
      }


      /**
       * Gets the price
       *
       * @return float
       */
      public function getPrice (): float
      {

         // Get the price
         return $this->price;
      }


      /**
       * Sets the price
       *
       * @param float $price The price
       */
      public function setPrice ( float $price ): void
      {

         // Set the price
         $this->price = $price;
      }

   }