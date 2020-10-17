<?php

   namespace Grayl\Store\Order\Storage;

   use Grayl\Database\Main\DatabasePorter;
   use Grayl\Date\DatePorter;
   use Grayl\Store\Order\Entity\OrderData;

   /**
    * Class OrderDataDatabaseMapper
    * The interface for finding order data in the MySQL database and turning them into objects / saving them to the database
    *
    * @package Grayl\Store\Order
    */
   class OrderDataDatabaseMapper
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
       * The DateFactory instance to interact with
       *
       * @var DatePorter
       */
      private DatePorter $date_porter;


      /**
       * The class constructor
       *
       * @param string         $table           The name of the database table to query
       * @param DatabasePorter $database_porter A fully configured DatabasePorter
       * @param DatePorter     $date_porter     The DatePorter class to generate DateControllers
       */
      public function __construct ( string $table,
                                    DatabasePorter $database_porter,
                                    DatePorter $date_porter )
      {

         // Set the database table to query
         $this->table = $table;

         // Set the DatabasePorter
         $this->database_porter = $database_porter;

         // Set the DatePorter instance
         $this->date_porter = $date_porter;
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
         if ( $result->countRows() > 0 ) {
            // Match found
            return true;
         }

         // No match
         return false;
      }


      /**
       * Gets an order data record from the database and turns it into an entity
       *
       * @param string $order_id The unique ID of the order
       *
       * @return OrderData
       * @throws \Exception
       */
      public function fetchOrderData ( string $order_id ): OrderData
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

         // Grab the first row found
         $row = $result->fetchNextRowAsArray();

         // Make sure a record was found
         if ( empty( $row ) ) {
            // Throw an error and exit
            throw new \Exception( 'Order data was not found.' );
         }

         // Create a new OrderData entity using the pulled data and return it
         return new OrderData( $this->date_porter->newDateControllerFromString( $row[ 'created' ] ),
                               $row[ 'order_id' ],
                               $row[ 'amount' ],
                               $row[ 'currency' ],
                               $row[ 'description' ],
                               $row[ 'ip_address' ] );
      }


      /**
       * Saves an order data entity into the database
       *
       * @param OrderData $order_data The OrderData entity to insert into the database
       *
       * @return int
       * @throws \Exception
       */
      public function saveOrderData ( OrderData $order_data ): int
      {

         // Return the ID of the inserted data
         return $this->insertOrderData( $order_data );
      }


      /**
       * Inserts an order data entity into the database
       *
       * @param OrderData $order_data The OrderData entity to insert into the database
       *
       * @return int
       * @throws \Exception
       */
      private function insertOrderData ( OrderData $order_data ): int
      {

         // Get a new InsertDatabaseController
         $request = $this->database_porter->newInsertDatabaseController( 'default' );

         // Build the insert statement
         $request->getQueryController()
                 ->insert( [ 'created'     => $order_data->getCreated()
                                                         ->getDateAsString(),
                             'order_id'    => $order_data->getOrderID(),
                             'amount'      => $order_data->getAmount(),
                             'currency'    => $order_data->getCurrency(),
                             'description' => $order_data->getDescription(),
                             'ip_address'  => $order_data->getIPAddress(), ] )
                 ->into( $this->table );

         // Run it and get the result
         $result = $request->runQuery();

         // Return the ID of the inserted data
         return $result->getReferenceID();
      }

   }