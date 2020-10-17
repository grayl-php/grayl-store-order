<?php

   namespace Grayl\Store\Order\Entity;

   use Grayl\Date\Controller\DateController;

   /**
    * Class OrderPayment
    * The entity for order payments
    *
    * @package Grayl\Payment
    */
   class OrderPayment
   {

      /**
       * The creation date of the payment
       *
       * @var DateController
       */
      private DateController $created;

      /**
       * The internal transaction ID of the associated order
       *
       * @var string
       */
      private string $order_id;

      /**
       * The reference id (external 3rd party transaction ID)
       *
       * @var string
       */
      private string $reference_id;

      /**
       * The processor of the payment
       *
       * @var string
       */
      private string $processor;

      /**
       * The total amount of the payment
       *
       * @var float
       */
      private float $amount;

      /**
       * The action of the payment (authorize, capture, etc.)
       *
       * @var string
       */
      private string $action;

      /**
       * If the payment action was successful
       *
       * @var bool
       */
      private bool $successful;

      /**
       * Extra data associated to this payment
       *
       * @var ?string
       */
      private ?string $metadata;


      /**
       * The class constructor
       *
       * @param DateController $date         The DateController object to set for creation
       * @param string         $order_id     The internal transaction ID of the associated order
       * @param string         $reference_id The external reference ID of this payment
       * @param string         $processor    The processor of this payment
       * @param float          $amount       The amount to charge
       * @param string         $action       The action of the payment (authorize, capture, etc.)
       * @param bool           $successful   If the payment action was successful
       * @param ?string        $metadata     The metadata to for the payment
       */
      public function __construct ( DateController $date,
                                    string $order_id,
                                    string $reference_id,
                                    string $processor,
                                    float $amount,
                                    string $action,
                                    bool $successful,
                                    ?string $metadata )
      {

         // Set the class data
         $this->setCreated( $date );
         $this->setOrderID( $order_id );
         $this->setReferenceID( $reference_id );
         $this->setProcessor( $processor );
         $this->setAmount( $amount );
         $this->setAction( $action );
         $this->setSuccessful( $successful );
         $this->setMetadata( $metadata );
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
       * Gets the reference id (external 3rd party transaction ID)
       *
       * @return string
       */
      public function getReferenceID (): string
      {

         // Get the reference ID
         return $this->reference_id;
      }


      /**
       * Sets the reference id (external 3rd party transaction ID)
       *
       * @param string $reference_id The external reference ID of this payment
       */
      public function setReferenceID ( string $reference_id ): void
      {

         // Set the reference ID
         $this->reference_id = $reference_id;
      }


      /**
       * Gets the processor
       *
       * @return string
       */
      public function getProcessor (): string
      {

         // Get the processor
         return $this->processor;
      }


      /**
       * Sets the processor
       *
       * @param string $processor The processor of this payment
       */
      public function setProcessor ( string $processor ): void
      {

         // Set the processor
         $this->processor = $processor;
      }


      /**
       * Gets the amount of the order
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
       * @param float $amount The amount to charge
       */
      public function setAmount ( float $amount ): void
      {

         // Set the amount
         $this->amount = $amount;
      }


      /**
       * Gets the action
       *
       * @return string
       */
      public function getAction (): string
      {

         // Get the action
         return $this->action;
      }


      /**
       * Sets the action
       *
       * @param string $action The action of the payment (authorize, capture, etc.)
       */
      public function setAction ( string $action ): void
      {

         // Set the action
         $this->action = $action;
      }


      /**
       * Gets the successful toggle
       *
       * @return bool
       */
      public function isSuccessful (): bool
      {

         // Get the successful field
         return $this->successful;
      }


      /**
       * Sets the successful toggle
       *
       * @param bool $successful If the payment action was successful
       */
      public function setSuccessful ( bool $successful ): void
      {

         // Set the successful toggle
         $this->successful = $successful;
      }


      /**
       * Gets the metadata
       *
       * @return ?string
       */
      public function getMetadata (): ?string
      {

         // Get the metadata
         return $this->metadata;
      }


      /**
       * Sets the metadata
       *
       * @param ?string $metadata The metadata to set
       */
      public function setMetadata ( ?string $metadata ): void
      {

         // Set the metadata
         $this->metadata = $metadata;
      }

   }