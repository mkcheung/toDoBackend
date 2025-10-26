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

}