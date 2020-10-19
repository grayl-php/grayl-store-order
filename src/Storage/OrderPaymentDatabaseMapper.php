<?php

   namespace Grayl\Store\Order\Storage;

   use Grayl\Database\Main\DatabasePorter;
   use Grayl\Date\DatePorter;
   use Grayl\Store\Order\Entity\OrderPayment;

   /**
    * Class OrderPaymentDatabaseMapper
    * The interface for finding order payments in the MySQL database and turning them into objects / saving them to the database
    *
    * @package Grayl\Store\Order
    */
   class OrderPaymentDatabaseMapper
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
       * Checks the database for a specific OrderPayment
       *
       * @param OrderPayment $payment The OrderPayment entity to find in the database
       *
       * @return bool
       * @throws \Exception
       */
      public function findOrderPayment ( OrderPayment $payment ): bool
      {

         // Get a new SelectDatabaseController
         $request = $this->database_porter->newSelectDatabaseController( 'default' );

         // Build the query object
         $request->getQueryController()
                 ->select( [ '*' ] )
                 ->from( $this->table )
                 ->where( 'created',
                          '=',
                          $payment->getCreated()
                                  ->getDateAsString() )
                 ->andWhere( 'order_id',
                             '=',
                             $payment->getOrderID() )
                 ->andWhere( 'reference_id',
                             '=',
                             $payment->getReferenceID() )
                 ->andWhere( 'processor',
                             '=',
                             $payment->getProcessor() )
                 ->andWhere( 'amount',
                             '=',
                             $payment->getAmount() )
                 ->andWhere( 'action',
                             '=',
                             $payment->getAction() )
                 ->andWhere( 'successful',
                             '=',
                             $payment->isSuccessful() );

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
       * Gets the latest payment record for an order from the database and turns it into an OrderPayment entity
       *
       * @param string $order_id The unique ID of the order associated to the payment
       *
       * @return OrderPayment
       * @throws \Exception
       */
      public function fetchOrderPayment ( string $order_id ): ?OrderPayment
      {

         // Get a new SelectDatabaseController
         $request = $this->database_porter->newSelectDatabaseController( 'default' );

         // Build the query object
         $request->getQueryController()
                 ->select( [ '*' ] )
                 ->from( $this->table )
                 ->where( 'order_id',
                          '=',
                          $order_id )
                 ->orderBy( [ 'created' ] )
                 ->direction( 'DESC' );

         // Run it and get the result
         $result = $request->runQuery();

         // Grab the first row found
         $row = $result->fetchNextRowAsArray();

         // Make sure we found something
         if ( empty( $row ) ) {
            // Simply exit, payment is not required
            return null;
         }

         // Create a new OrderPayment entity using the pulled data and return it
         return new OrderPayment( $this->date_porter->newDateControllerFromString( $row[ 'created' ] ),
                                  $row[ 'order_id' ],
                                  $row[ 'reference_id' ],
                                  $row[ 'processor' ],
                                  $row[ 'amount' ],
                                  $row[ 'action' ],
                                  (bool) $row[ 'successful' ],
                                  $row[ 'metadata' ] );
      }


      /**
       * Determines whether to update or insert an OrderPayment entity
       *
       * @param OrderPayment $payment The OrderPayment entity to save in the database
       *
       * @throws \Exception
       */
      public function saveOrderPayment ( OrderPayment $payment ): void
      {

         // See if the OrderPayment already exists in the database
         if ( $this->findOrderPayment( $payment ) ) {
            // Update the existing record
            $this->updateOrderPayment( $payment );
         } // Otherwise insert a new record
         else {
            // Insert a new record
            $this->insertOrderPayment( $payment );
         }
      }


      /**
       * Inserts an OrderPayment entity into the database
       *
       * @param OrderPayment $payment The OrderPayment entity to insert into the database
       *
       * @return int
       * @throws \Exception
       */
      private function insertOrderPayment ( OrderPayment $payment ): int
      {

         // Get a new InsertDatabaseController
         $request = $this->database_porter->newInsertDatabaseController( 'default' );

         // Build the insert statement
         $request->getQueryController()
                 ->insert( [ 'created'      => $payment->getCreated()
                                                       ->getDateAsString(),
                             'order_id'     => $payment->getOrderID(),
                             'reference_id' => $payment->getReferenceID(),
                             'processor'    => $payment->getProcessor(),
                             'amount'       => $payment->getAmount(),
                             'action'       => $payment->getAction(),
                             'successful'   => (int) $payment->isSuccessful(),
                             'metadata'     => $payment->getMetadata(), ] )
                 ->into( $this->table );

         // Run it and get the result
         $result = $request->runQuery();

         // Return the ID of the inserted data
         return $result->getReferenceID();
      }


      /**
       * Updates the metadata of an OrderPayment entity in the database
       *
       * @param OrderPayment $payment The OrderPayment entity to update into the database
       *
       * @return int
       * @throws \Exception
       */
      private function updateOrderPayment ( OrderPayment $payment ): int
      {

         // Get a new UpdateDatabaseController
         $request = $this->database_porter->newUpdateDatabaseController( 'default' );

         // Build the update statement
         $request->getQueryController()
                 ->update( $this->table )
                 ->set( [ 'metadata' => $payment->getMetadata() ] )
                 ->where( 'created',
                          '=',
                          $payment->getCreated()
                                  ->getDateAsString() )
                 ->andWhere( 'order_id',
                             '=',
                             $payment->getOrderID() )
                 ->andWhere( 'reference_id',
                             '=',
                             $payment->getReferenceID() )
                 ->andWhere( 'processor',
                             '=',
                             $payment->getProcessor() )
                 ->andWhere( 'amount',
                             '=',
                             $payment->getAmount() )
                 ->andWhere( 'action',
                             '=',
                             $payment->getAction() )
                 ->andWhere( 'successful',
                             '=',
                             $payment->isSuccessful() );

         // Run it and get the result
         $result = $request->runQuery();

         // Return the ID of the updated data
         return $result->getReferenceID();
      }

   }