<?php

   namespace Grayl\Store\Order\Entity;

   use Grayl\Mixin\Common\Entity\FlatDataBag;

   /**
    * Class OrderItemBag
    * The entity of a bag of OrderItem entities
    *
    * @package Grayl\Store\Order
    */
   class OrderItemBag
   {

      /**
       * An FlatDataBag of OrderItem entities
       *
       * @var FlatDataBag
       */
      private FlatDataBag $items;


      /**
       * The class constructor
       */
      public function __construct ()
      {

         // Create the FlatDataBag
         $this->items = new FlatDataBag();
      }


      /**
       * Puts a new OrderItem entity into the bag of items
       *
       * @param OrderItem $item The order item entity to store
       */
      public function putOrderItem ( OrderItem $item ): void
      {

         // Store the item in the bag
         $this->items->putPiece( $item );
      }


      /**
       * Returns the array of items in the bag
       *
       * @return OrderItem[]
       */
      public function getOrderItems (): array
      {

         // Return all items
         return $this->items->getPieces();
      }

   }