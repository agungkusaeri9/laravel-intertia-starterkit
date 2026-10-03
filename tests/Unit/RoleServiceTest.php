<?php

namespace Tests\Unit;

use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Services\ActivityLogService;
use App\Services\RoleService;
use Mockery;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleServiceTest extends TestCase
{
    private $roleRepositoryMock;

    private $activityLogServiceMock;

    private RoleService $roleService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->roleRepositoryMock = Mockery::mock(RoleRepositoryInterface::class);
        $this->activityLogServiceMock = Mockery::mock(ActivityLogService::class);

        $this->roleService = new RoleService(
            $this->roleRepositoryMock,
            $this->activityLogServiceMock
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_role_by_id_returns_role()
    {
        $role = new Role(['id' => 1, 'name' => 'admin']);
        $this->roleRepositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with(1)
            ->andReturn($role);

        $result = $this->roleService->getRoleById(1);

        $this->assertSame($role, $result);
    }

    public function test_update_role_throws_exception_when_role_not_found()
    {
        $role = new Role;
        $role->id = 999;

        $this->roleRepositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with(999)
            ->andReturn(null);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Data peran tidak ditemukan di sistem.');

        $this->roleService->updateRole($role, ['name' => 'Editor']);
    }

    public function test_delete_role_throws_exception_when_role_not_found()
    {
        $role = new Role;
        $role->id = 999;

        $this->roleRepositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with(999)
            ->andReturn(null);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Data peran tidak ditemukan di sistem.');

        $this->roleService->deleteRole($role);
    }

    public function test_delete_super_admin_role_throws_exception()
    {
        $role = new Role;
        $role->id = 1;
        $role->name = 'super admin';

        $this->roleRepositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with(1)
            ->andReturn($role);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Peran super admin tidak dapat dihapus.');

        $this->roleService->deleteRole($role);
    }
}
