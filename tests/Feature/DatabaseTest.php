<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable('users'));
    }

    public function test_warehouses_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable('warehouses'));
    }

    public function test_categories_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable('categories'));
    }

    public function test_units_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable('units'));
    }

    public function test_items_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable('items'));
    }

    public function test_stock_balances_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable('stock_balances'));
    }

    public function test_stock_transactions_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable('stock_transactions'));
    }

    public function test_stock_receipts_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable('stock_receipts'));
    }

    public function test_stock_receipt_items_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable('stock_receipt_items'));
    }

    public function test_stock_issues_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable('stock_issues'));
    }

    public function test_stock_issue_items_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable('stock_issue_items'));
    }

    public function test_stock_transfers_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable('stock_transfers'));
    }

    public function test_stock_transfer_items_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable('stock_transfer_items'));
    }

    public function test_audit_logs_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable('audit_logs'));
    }

    public function test_roles_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable('roles'));
    }

    public function test_permissions_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable('permissions'));
    }

    public function test_users_table_has_required_columns(): void
    {
        $columns = ['id', 'name', 'email', 'phone', 'is_active', 'password', 'created_at', 'updated_at', 'deleted_at'];

        foreach ($columns as $column) {
            $this->assertTrue(
                Schema::hasColumn('users', $column),
                "Users table should have '{$column}' column"
            );
        }
    }

    public function test_warehouses_table_has_required_columns(): void
    {
        $columns = ['id', 'code', 'name', 'location', 'manager_name', 'phone', 'is_active', 'notes'];

        foreach ($columns as $column) {
            $this->assertTrue(
                Schema::hasColumn('warehouses', $column),
                "Warehouses table should have '{$column}' column"
            );
        }
    }

    public function test_items_table_has_foreign_keys(): void
    {
        $this->assertTrue(Schema::hasColumn('items', 'category_id'));
        $this->assertTrue(Schema::hasColumn('items', 'unit_id'));
    }

    public function test_stock_balances_has_unique_constraint(): void
    {
        $this->assertTrue(Schema::hasColumn('stock_balances', 'item_id'));
        $this->assertTrue(Schema::hasColumn('stock_balances', 'warehouse_id'));
        $this->assertTrue(Schema::hasColumn('stock_balances', 'quantity'));
    }
}