<?php

   namespace Grayl\Store\Order\Helper;

   use Grayl\Mixin\Common\Traits\StaticTrait;
   use Grayl\Store\Order\Controller\OrderController;
   use Grayl\Store\Order\OrderPorter;

   /**
    * A package of miscellaneous top-level functions for working with Orders
    * These are kept isolated to maintain separation between the main library and specific user functionality
    *
    * @package Grayl\Store\Order
    */
   class OrderHelper
   {

      // Use the static instance trait
      use StaticTrait;

      /**
       * Cancels an open order by creating a new OrderPayment
       *
       * @param OrderController $order The OrderController entity to cancel
       *
       * @return bool
       * @throws \Exception
       */
      public function cancelOrderController ( OrderController $order ): bool
      {

         // Only proceed if the order is not paid
         if ( $order->isOrderPaid() == true ) {
            // Order payment is final, cannot be canceled
            return false;
         }

         // Use the OrderController to create and set the new payment log
         $order->setOrderPayment( OrderPorter::getInstance()
                                             ->newOrderPayment( $order->getOrderID(),
                                                                'system',
                                                                'system',
                                                                0,
                                                                'cancel',
                                                                true,
                                                                null ) );

         // Save the order
         $order->saveOrder();

         // Success
         return true;
      }

   }