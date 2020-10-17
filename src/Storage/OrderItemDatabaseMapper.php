<?php

   namespace Grayl\Store\Order\Storage;

   use Grayl\Database\Main\DatabasePorter;
   use Grayl\Store\Order\Entity\OrderItem;
   use Grayl\Store\Order\Entity\OrderItemBag;

   /**
    * Class OrderItemDatabaseMapper
    * The interface for finding order items in the MySQL database and turning them into objects / saving them to the database
    *
    * @package Grayl\Store\Order
    */
   class OrderItemDatabaseMapper
   {

      /**
       * The name of the database table to query
       *
       * @var string
       */
      private string $table;

      /**
       * A fully configured DatabasePorter
       *
       * @var DatabasePorter
       */
      private DatabasePorter $database_porter;


      /**
       * The class constructor
       *
       * @param string         $table           The name of the database table to query
       * @param DatabasePorter $database_porter A fully configured DatabasePorter
       */
      public function __construct ( string $table,
                                    DatabasePorter $database_porter )
      {

         // Set the database table to query
         $this->table = $table;

         // Set the DatabasePorter
         $this->database_porter = $database_porter;
      }


      /**
       * Gets all OrderItem entities for an order from the database and returns them in an OrderItemBag
       *
       * @param string $order_id The unique ID of the order
       *
       * @return OrderItemBag
       * @throws \Exception
       */
      public function fetchOrderItemBag ( string $order_id ): OrderItemBag
      {

         // Get a new SelectDatabaseController
         $request = $this->database_porter->newSelectDatabaseController( 'default' );

         // Build the query object
         $request->getQueryController()
                 ->select( [ '*' ] )
                 ->from( $this->table )
                 ->where( 'order_id',
                          '=',
                          $order_id );

         // Run it and get the result
         $result = $request->runQuery();

         // Make sure we found something
         if ( empty( $result->countRows() ) ) {
            // Throw an error and exit
            throw new \Exception( 'No order items found.' );
         }

         // Create a new order item bag entity
         $bag = new OrderItemBag();

         // Loop through each item found and turn it into an entity
         while ( $row = $result->fetchNextRowAsArray() ) {
            // Create a new order item entity
            $item = $this->newOrderItemFromDatabase( $row );

            // Put the new entity into the bag
            $bag->putOrderItem( $item );
         }

         // Return the entity
         return $bag;
      }


      /**
       * Creates an OrderItem entity from a record in the database
       *
       * @param array $row The database row to convert into an item
       *
       * @return OrderItem
       * @throws \Exception
       */
      private function newOrderItemFromDatabase ( array $row ): OrderItem
      {

         // Make sure we have something
         if ( empty( $row[ 'sku' ] ) ) {
            // Throw an error and exit
            throw new \Exception( 'Order item was empty.' );
         }

         // Create a new order item entity from the row's data
         return new OrderItem( $row[ 'order_id' ],
                               $row[ 'sku' ],
                               $row[ 'name' ],
                               $row[ 'quantity' ],
                               $row[ 'price' ] );
      }


      /**
       * Inserts the contents of an OrderItemBag into the database
       *
       * @param OrderItemBag $bag The OrderItemBag entity to insert into the database
       *
       * @throws \Exception
       */
      public function saveOrderItemBag ( OrderItemBag $bag ): void
      {

         // Loop through each item in the bag and insert it
         foreach ( $bag->getOrderItems() as $item ) {
            // Insert the individual item
            $this->insertOrderItem( $item );
         }
      }


      /**
       * Inserts an OrderItem into the database
       *
       * @param OrderItem $item The OrderItem entity to insert into the database
       *
       * @return int
       * @throws \Exception
       */
      private function insertOrderItem ( OrderItem $item ): int
      {

         // Get a new InsertDatabaseController
         $request = $this->database_porter->newInsertDatabaseController( 'default' );

         // Build the insert statement
         $request->getQueryController()
                 ->insert( [ 'order_id' => $item->getOrderID(),
                             'sku'      => $item->getSKU(),
                             'name'     => $item->getName(),
                             'quantity' => $item->getQuantity(),
                             'price'    => $item->getPrice(), ] )
                 ->into( $this->table );

         // Run it and get the result
         $result = $request->runQuery();

         // Return the ID of the inserted data
         return $result->getReferenceID();
      }

   }