<?php
namespace App\Providers;

use App\Models\User;
use App\Models\Warehouse;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Item;
use App\Models\StockReceipt;
use App\Models\StockIssue;
use App\Models\StockTransfer;
use App\Observers\UserObserver;
use App\Observers\WarehouseObserver;
use App\Observers\CategoryObserver;
use App\Observers\UnitObserver;
use App\Observers\ItemObserver;
use App\Observers\StockReceiptObserver;
use App\Observers\StockIssueObserver;
use App\Observers\StockTransferObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        User::observe(UserObserver::class);
        Warehouse::observe(WarehouseObserver::class);
        Category::observe(CategoryObserver::class);
        Unit::observe(UnitObserver::class);
        Item::observe(ItemObserver::class);
        StockReceipt::observe(StockReceiptObserver::class);
        StockIssue::observe(StockIssueObserver::class);
        StockTransfer::observe(StockTransferObserver::class);
    }
}