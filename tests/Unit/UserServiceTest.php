<?php

namespace Tests\Unit;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\ActivityLogService;
use App\Services\UserService;
use Mockery;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    private $userRepositoryMock;

    private $activityLogServiceMock;

    private UserService $userService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepositoryMock = Mockery::mock(UserRepositoryInterface::class);
        $this->activityLogServiceMock = Mockery::mock(ActivityLogService::class);

        $this->userService = new UserService(
            $this->userRepositoryMock,
            $this->activityLogServiceMock
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_user_by_id_returns_user()
    {
        $user = new User(['name' => 'John Doe', 'username' => 'johndoe']);
        $user->id = 1;
        $this->userRepositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with(1)
            ->andReturn($user);

        $result = $this->userService->getUserById(1);

        $this->assertSame($user, $result);
    }

    public function test_update_user_throws_exception_when_user_not_found()
    {
        $user = new User;
        $user->id = 999;

        $this->userRepositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with(999)
            ->andReturn(null);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Data pengguna tidak ditemukan di sistem.');

        $this->userService->updateUser($user, ['name' => 'Updated Name']);
    }

    public function test_delete_user_throws_exception_when_user_not_found()
    {
        $user = new User;
        $user->id = 999;

        $this->userRepositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with(999)
            ->andReturn(null);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Data pengguna tidak ditemukan di sistem.');

        $this->userService->deleteUser($user);
    }
}
