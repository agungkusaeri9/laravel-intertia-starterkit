<?php

namespace Tests\Unit;

use App\Repositories\Contracts\PermissionRepositoryInterface;
use App\Services\PermissionService;
use Mockery;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class PermissionServiceTest extends TestCase
{
    private $permissionRepositoryMock;

    private PermissionService $permissionService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->permissionRepositoryMock = Mockery::mock(PermissionRepositoryInterface::class);
        $this->permissionService = new PermissionService($this->permissionRepositoryMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_permission_by_id_returns_permission()
    {
        $permission = new Permission(['id' => 1, 'name' => 'create users']);
        $this->permissionRepositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with(1)
            ->andReturn($permission);

        $result = $this->permissionService->getPermissionById(1);

        $this->assertSame($permission, $result);
    }
}
