<?php

namespace App\Repositories\Eloquent;

use App\Models\Order;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class OrderRepository implements OrderRepositoryInterface
{
    public function __construct(private Order $model) {}

    public function create(array $data): Order
    {
        return $this->model->create($data);
    }

    public function findById(int $id): ?Order
    {
        return $this->model->with('items.product')->find($id);
    }

    public function findByOrderNumber(string $orderNumber): ?Order
    {
        return $this->model->with('items.product')->where('order_number', $orderNumber)->first();
    }

    public function getByUser(int $userId): Collection
    {
        return $this->model->with('items')->where('user_id', $userId)->latest()->get();
    }

    public function updateStatus(int $id, string $status): bool
    {
        return $this->model->where('id', $id)->update(['status' => $status]) > 0;
    }
}
