<?php

namespace Tests\Feature;

use App\Models\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_category_supports_hierarchy_and_soft_delete(): void
    {
        $parent = ProductCategory::create([
            'code' => 'CAT-ROOT-001',
            'name' => 'Root category',
            'description' => 'Root category description',
            'is_active' => true,
        ]);

        $child = ProductCategory::create([
            'parent_id' => $parent->id,
            'code' => 'CAT-CHILD-001',
            'name' => 'Child category',
            'description' => 'Child category description',
            'is_active' => false,
        ]);

        $this->assertEquals($parent->id, $child->parent->id);
        $this->assertCount(1, $parent->children);
        $this->assertFalse($child->is_active);

        $child->delete();

        $this->assertNotNull($child->deleted_at);
        $this->assertDatabaseHas('product_categories', ['id' => $child->id, 'deleted_at' => $child->deleted_at]);
    }
}
