<?php
namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\TaskController;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TaskControllerTest extends TestCase{
    use DatabaseTransactions;

    public function testIndex(){
        $paginationCount = 20;
        $taskController = $this->app->make(TaskController::class);
        $request = new Request();
        $request->setMethod('GET');
        $user = User::factory()->create();
        $request->setUserResolver(fn() => $user);
        $tasks = [];
        $this->actingAs($user);
        for($i = 0; $i < 21; $i++){
            $tasks[] = Task::factory()->forUser($user->id)->create();
        }
        $response = $taskController->index($request);
        $this->assertCount($paginationCount, $response);
        $initialIdForDesc = $response[0]->id;
        $bottomLimitId = $initialIdForDesc - $paginationCount;
        for($i = $initialIdForDesc, $j = 0; $i > $bottomLimitId; $i--, $j++){
            $this->assertEquals($i, $response[$j]->id);
        }
    }

    public function testStore(){
        $taskController = $this->app->make(TaskController::class);
        $user = User::factory()->create();
        $request = Request::create('/api/tasks', 'POST', [
            'task' => 'New Task',
            'priority' => 'medium',
            'due_date' => now(),
            'done' => false
        ]);
        $request->setUserResolver(fn() => $user);
        $jsonResponse = $taskController->store($request);
        $this->assertSame(201, $jsonResponse->getStatusCode());
        $this->assertDatabaseHas('tasks', [
            'task'    => 'New Task',
            'user_id' => $user->id,
            'done'    => 0, // boolean stored as tinyint
        ]);
    }

    public function testShow(){
        $taskController = $this->app->make(TaskController::class);
        $user = User::factory()->create();
        $createRequest = Request::create('/api/tasks', 'POST', [
            'task' => 'New Task',
            'priority' => 'medium',
            'due_date' => now(),
            'done' => false
        ]);
        $createRequest->setUserResolver(fn() => $user);
        $jsonResponse = $taskController->store($createRequest);
        $this->assertSame(201, $jsonResponse->getStatusCode());
        $this->assertDatabaseHas('tasks', [
            'task'    => 'New Task',
            'user_id' => $user->id,
            'done'    => 0, // boolean stored as tinyint
        ]);
        $newTask = Task::find($jsonResponse->getData(true)['id']);
        $this->actingAs($user);
        $showRequest = Request::create("/api/tasks/{$newTask->id}", 'GET');
        $showRequest->setUserResolver(fn() => $user);
        $showTaskResult = $taskController->show($showRequest,$newTask);
        $this->assertEquals($newTask->id, $showTaskResult->id);
    }

    public function testUpdate(){
        $taskController = $this->app->make(TaskController::class);
        $user = User::factory()->create();
        $createRequest = Request::create('/api/tasks', 'POST', [
            'task' => 'New Task',
            'priority' => 'medium',
            'due_date' => now(),
            'done' => false
        ]);
        $createRequest->setUserResolver(fn() => $user);
        $jsonResponse = $taskController->store($createRequest);
        $this->assertSame(201, $jsonResponse->getStatusCode());
        $this->assertDatabaseHas('tasks', [
            'task'    => 'New Task',
            'user_id' => $user->id,
            'done'    => 0, // boolean stored as tinyint
        ]);
        $newTask = Task::find($jsonResponse->getData(true)['id']);
        $this->actingAs($user);
        $updateRequest = Request::create("/api/tasks/{$newTask->id}", 'UPDATE', [
            'task' => 'Update Task'
        ]);
        $updateTaskResult = $taskController->update($updateRequest, $newTask);
        $this->assertEquals($updateTaskResult->task, 'Update Task');
    }

    public function testDestroy(){
        $taskController = $this->app->make(TaskController::class);
        $user = User::factory()->create();
        $createRequest = Request::create('/api/tasks', 'POST', [
            'task' => 'New Task',
            'priority' => 'medium',
            'due_date' => now(),
            'done' => false
        ]);
        $createRequest->setUserResolver(fn() => $user);
        $jsonResponse = $taskController->store($createRequest);
        $this->assertSame(201, $jsonResponse->getStatusCode());
        $this->assertDatabaseHas('tasks', [
            'task'    => 'New Task',
            'user_id' => $user->id,
            'done'    => 0, // boolean stored as tinyint
        ]);
        $newTask = Task::find($jsonResponse->getData(true)['id']);
        $this->actingAs($user);
        $deleteRequest = Request::create("/api/tasks/{$newTask->id}", 'DELETE');
        $deleteTaskResult = $taskController->destroy($deleteRequest, $newTask);
        $this->assertDatabaseMissing('tasks', [
            'id' => $newTask->id,
        ]);

    }
}