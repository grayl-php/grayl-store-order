<?php

   namespace Grayl\Store\Order\Storage;

   use Grayl\Database\Main\DatabasePorter;
   use Grayl\Store\Order\Entity\OrderCustomer;

   /**
    * Class OrderCustomerDatabaseMapper
    * The interface for finding order customers in the MySQL database and turning them into objects / saving them to the database
    *
    * @package Grayl\Store\Order
    */
   class OrderCustomerDatabaseMapper
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
       * Checks the database for a specific customer
       *
       * @param OrderCustomer $customer The OrderCustomer entity to find in the database
       *
       * @return bool
       * @throws \Exception
       */
      public function findOrderCustomer ( OrderCustomer $customer ): bool
      {

         // Get a new SelectDatabaseController
         $request = $this->database_porter->newSelectDatabaseController( 'default' );

         // Build the query object
         $request->getQueryController()
                 ->select( [ '*' ] )
                 ->from( $this->table )
                 ->where( 'order_id',
                          '=',
                          $customer->getOrderID() )
                 ->andWhere( 'email_address',
                             '=',
                             $customer->getEmailAddress() );

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
       * Gets a customer record from the database and turns it into an entity
       *
       * @param string $order_id The unique ID of the order associated to the customer
       *
       * @return OrderCustomer
       * @throws \Exception
       */
      public function fetchOrderCustomer ( string $order_id ): ?OrderCustomer
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

         // Make sure we found something
         if ( empty( $row ) ) {
            return null;
         }

         // Create a new order customer entity using the pulled data and return it
         return new OrderCustomer( $row[ 'order_id' ],
                                   $row[ 'first_name' ],
                                   $row[ 'last_name' ],
                                   $row[ 'email_address' ],
                                   $row[ 'address_1' ],
                                   $row[ 'address_2' ],
                                   $row[ 'city' ],
                                   $row[ 'state' ],
                                   $row[ 'postcode' ],
                                   $row[ 'country' ],
                                   $row[ 'phone_number' ] );
      }


      /**
       * Determines whether to insert an OrderCustomer entity
       *
       * @param OrderCustomer $customer The OrderCustomer entity to save into the database
       *
       * @throws \Exception
       */
      public function saveOrderCustomer ( OrderCustomer $customer ): void
      {

         // See if the OrderCustomer already exists in the database
         if ( ! $this->findOrderCustomer( $customer ) ) {
            // Insert a new record
            $this->insertOrderCustomer( $customer );
         }
      }


      /**
       * Inserts an order customer entity into the database
       *
       * @param OrderCustomer $customer The OrderCustomer entity to insert into the database
       *
       * @return int
       * @throws \Exception
       */
      private function insertOrderCustomer ( OrderCustomer $customer ): int
      {

         // Get a new InsertDatabaseController
         $request = $this->database_porter->newInsertDatabaseController( 'default' );

         // Build the insert statement
         $request->getQueryController()
                 ->insert( [ 'order_id'      => $customer->getOrderID(),
                             'first_name'    => $customer->getFirstName(),
                             'last_name'     => $customer->getLastName(),
                             'email_address' => $customer->getEmailAddress(),
                             'address_1'     => $customer->getAddress1(),
                             'address_2'     => $customer->getAddress2(),
                             'city'          => $customer->getCity(),
                             'state'         => $customer->getState(),
                             'postcode'      => $customer->getPostcode(),
                             'country'       => $customer->getCountry(),
                             'phone_number'  => $customer->getPhoneNumber(), ] )
                 ->into( $this->table );

         // Run it and get the result
         $result = $request->runQuery();

         // Return the ID of the inserted data
         return $result->getReferenceID();
      }

   }