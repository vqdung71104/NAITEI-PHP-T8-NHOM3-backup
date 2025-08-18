<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrderPolicy
{
    /**
     * Determine whether the user can view any orders.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the order.
     */
    public function view(User $user, Order $order): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        
        // Customer chỉ có thể xem order của mình
        return $order->belongsToUser($user->id);
    }

    /**
     * Determine whether the user can create orders.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the order.
     */
    public function update(User $user, Order $order): bool
    {
        // Admin có thể update bất kỳ order nào
        if ($user->isAdmin()) {
            return true;
        }
        
        return $order->belongsToUser($user->id) && $order->status === Order::STATUS_PENDING;
    }

    /**
     * Determine whether the user can delete the order.
     */
    public function delete(User $user, Order $order): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        
        return $order->belongsToUser($user->id) && 
               in_array($order->status, [Order::STATUS_PENDING, Order::STATUS_CANCELLED]);
    }

    /**
     * Determine whether the user can cancel the order.
     */
    public function cancel(User $user, Order $order): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        
        return $order->belongsToUser($user->id) && $order->canBeCancelled();
    }

    /**
     * Determine whether the user can update order status.
     */
    public function updateStatus(User $user, Order $order): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view order statistics.
     */
    public function viewStatistics(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the order.
     */
    public function restore(User $user, Order $order): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the order.
     */
    public function forceDelete(User $user, Order $order): bool
    {
        return $user->isAdmin();
    }
}