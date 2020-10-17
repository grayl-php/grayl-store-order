<?php

   namespace Grayl\Store\Order\Service;

   use Grayl\Store\Order\Entity\OrderData;
   use Grayl\Store\Order\Entity\OrderItem;
   use Grayl\Store\Order\Entity\OrderItemBag;
   use Grayl\Store\Order\Entity\OrderPayment;

   /**
    * Class OrderService
    * The service for working with Order entities
    *
    * @package Grayl\Store\Order
    */
   class OrderService
   {

      /**
       * An array of payment actions that are considered "fail"
       *
       * @var String[]
       */
      private array $fail_actions;

      /**
       * An array of payment actions that are considered "pending"
       *
       * @var String[]
       */
      private array $pending_actions;

      /**
       * An array of payment actions that are considered "complete"
       *
       * @var String[]
       */
      private array $complete_actions;


      /**
       * The class constructor
       *
       * @param String[] $fail_actions     An array of payment actions that are considered "fail"
       * @param String[] $pending_actions  An array of payment actions that are considered "pending"
       * @param String[] $complete_actions An array of payment actions that are considered "complete"
       */
      public function __construct ( array $fail_actions,
                                    array $pending_actions,
                                    array $complete_actions )
      {

         // Set the class data
         $this->fail_actions     = $fail_actions;
         $this->pending_actions  = $pending_actions;
         $this->complete_actions = $complete_actions;
      }


      /**
       * Resets the order amount based on the total of its OrderItem entities
       *
       * @param OrderData    $order_data     The OrderData entity to save the new amount to
       * @param OrderItemBag $order_item_bag The OrderItemBag entity to use for the calculation of the new amount
       */
      public function recalculateOrderAmount ( OrderData $order_data,
                                               OrderItemBag $order_item_bag ): void
      {

         // Save the item entity into the bag
         $amount = $this->calculateOrderItemBagTotal( $order_item_bag );

         // Set the amount
         $order_data->setAmount( $amount );
      }


      /**
       * Determines if an order is paid based on its current OrderPayment entity
       *
       * @param OrderData     $order_data    The OrderData entity to use
       * @param ?OrderPayment $order_payment The OrderPayment entity to use
       *
       * @return bool
       */
      public function isOrderPaid ( OrderData $order_data,
                                    ?OrderPayment $order_payment ): bool
      {

         // Compare the order totals, payment totals, and the health of the payment
         if ( ! empty( $order_payment ) && $this->isOrderPaymentCompleted( $order_payment ) && $order_payment->getAmount() >= $order_data->getAmount() ) {
            // Order has been paid
            return true;
         }

         // Not paid
         return false;
      }


      /**
       * Determines if an order is closed from further modification
       *
       * @param ?OrderPayment $order_payment The OrderPayment entity to use
       *
       * @return bool
       */
      public function isOrderClosed ( ?OrderPayment $order_payment ): bool
      {

         // Compare the order totals, payment totals, and the health of the payment
         if ( ! empty( $order_payment ) && ( $this->isOrderPaymentFailed( $order_payment ) || $this->isOrderPaymentCompleted( $order_payment ) ) ) {
            // Closed from further modification
            return true;
         }

         // Not closed
         return false;
      }


      /**
       * Determines if an OrderPayment is in failed status
       *
       * @param OrderPayment $order_payment The OrderPayment entity to check
       *
       * @return bool
       */
      public function isOrderPaymentFailed ( OrderPayment $order_payment ): bool
      {

         // If the action was not successful or is a fail action
         if ( $order_payment->isSuccessful() && in_array( $order_payment->getAction(),
                                                          $this->fail_actions ) ) {
            // Failed
            return true;
         }

         // Not failed
         return false;
      }


      /**
       * Determines if an OrderPayment is in completed status
       *
       * @param OrderPayment $order_payment The OrderPayment entity to check
       *
       * @return bool
       */
      public function isOrderPaymentCompleted ( OrderPayment $order_payment ): bool
      {

         // If the action was successful and is a complete action
         if ( $order_payment->isSuccessful() && in_array( $order_payment->getAction(),
                                                          $this->complete_actions ) ) {
            // Completed
            return true;
         }

         // Not completed
         return false;
      }


      /**
       * Places a new OrderItem entity into an OrderItemBag and updates the order amount
       *
       * @param OrderData    $order_data     The OrderData entity to use
       * @param OrderItemBag $order_item_bag The OrderItemBag entity to save the new item into
       * @param OrderItem    $order_item     The OrderItem entity to add into the bag
       */
      public function putOrderItem ( OrderData $order_data,
                                     OrderItemBag $order_item_bag,
                                     OrderItem $order_item ): void
      {

         // Place the OrderItem entity into the bag
         $order_item_bag->putOrderItem( $order_item );

         // Recalculate the order amount based on all of its items
         $this->recalculateOrderAmount( $order_data,
                                        $order_item_bag );
      }


      /**
       * Calculates the total amount of all items and sets it for the order
       *
       * @param OrderItemBag $bag The OrderItemBag entity to calculate the total for
       *
       * @return float
       */
      public function calculateOrderItemBagTotal ( OrderItemBag $bag ): float
      {

         // The default amount is 0
         $order_amount = 0;

         // Loop through each item in the bag and add it to the amount
         foreach ( $bag->getOrderItems() as $item ) {
            // Multiply the item's price by its quantity
            $item_price = bcmul( $item->getPrice(),
                                 $item->getQuantity(),
                                 2 );

            // Add the item's final price to the running amount
            $order_amount = bcadd( $item_price,
                                   $order_amount,
                                   2 );
         }

         // Return the amount
         return $order_amount;
      }

   }