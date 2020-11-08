<?php

   namespace Grayl\Store\Order\Config;

   use Grayl\Mixin\Common\Entity\FlatDataBag;

   /**
    * Class OrderConfig
    * The config class for the order package
    *
    * @package Grayl\Store\Order
    */
   class OrderConfig
   {

      /**
       * A bag of failed payment actions
       *
       * @var FlatDataBag
       */
      protected FlatDataBag $failed_payment_actions;

      /**
       * A bag of pending payment actions
       *
       * @var FlatDataBag
       */
      protected FlatDataBag $pending_payment_actions;

      /**
       * A bag of completed payment actions
       *
       * @var FlatDataBag
       */
      protected FlatDataBag $completed_payment_actions;


      /**
       * Class constructor
       *
       * @param string[] $failed_payment_actions    An array of failed payment actions
       * @param string[] $pending_payment_actions   An array of pending payment actions
       * @param string[] $completed_payment_actions An array of completed payment actions
       */
      public function __construct ( array $failed_payment_actions,
                                    array $pending_payment_actions,
                                    array $completed_payment_actions )
      {

         // Create the required bags
         $this->failed_payment_actions    = new FlatDataBag();
         $this->pending_payment_actions   = new FlatDataBag();
         $this->completed_payment_actions = new FlatDataBag();

         // Set the class data
         $this->setFailedPaymentActions( $failed_payment_actions );
         $this->setPendingPaymentActions( $pending_payment_actions );
         $this->setCompletedPaymentActions( $completed_payment_actions );
      }


      /**
       * Gets all failed payment actions
       *
       * @return string[]
       */
      public function getFailedPaymentActions (): array
      {

         // Return all failed payment actions
         return $this->failed_payment_actions->getPieces();
      }


      /**
       * Sets an array of failed payment actions
       *
       * @param string[] $failed_payment_actions An array of failed payment actions
       */
      public function setFailedPaymentActions ( array $failed_payment_actions ): void
      {

         // Set the actions
         $this->failed_payment_actions->putPieces( $failed_payment_actions );
      }


      /**
       * Gets all pending payment actions
       *
       * @return string[]
       */
      public function getPendingPaymentActions (): array
      {

         // Return all pending payment actions
         return $this->pending_payment_actions->getPieces();
      }


      /**
       * Sets an array of pending payment actions
       *
       * @param string[] $pending_payment_actions An array of pending payment actions
       */
      public function setPendingPaymentActions ( array $pending_payment_actions ): void
      {

         // Set the actions
         $this->pending_payment_actions->putPieces( $pending_payment_actions );
      }


      /**
       * Gets all completed payment actions
       *
       * @return string[]
       */
      public function getCompletedPaymentActions (): array
      {

         // Return all completed payment actions
         return $this->completed_payment_actions->getPieces();
      }


      /**
       * Sets an array of completed payment actions
       *
       * @param string[] $completed_payment_actions An array of completed payment actions
       */
      public function setCompletedPaymentActions ( array $completed_payment_actions ): void
      {

         // Set the actions
         $this->completed_payment_actions->putPieces( $completed_payment_actions );
      }
   }