<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Services\TaskService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class TaskController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected TaskService $taskService
    ) {}

    #[OA\Get(
        path: "/api/tasks",
        summary: "لیست تسک های کاربر",
        description: "دریافت لیست تمام تسک های کاربر لاگین شده",
        tags: ["Tasks"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "لیست تسک ها با موفقیت دریافت شد",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Tasks retrieved successfully"),
                        new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: "id", type: "integer", example: 1),
                                    new OA\Property(property: "title", type: "string", example: "طراحی صفحه اصلی"),
                                    new OA\Property(property: "status", type: "string", example: "in_progress"),
                                    new OA\Property(property: "priority", type: "string", example: "high"),
                                    new OA\Property(property: "deadline", type: "string", format: "date", example: "2026-10-25"),
                                ]
                            )
                        ),
                    ]
                )
            ),
            new OA\Response(response: 401, description: "عدم احراز هویت"),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $tasks = $this->taskService->listTasks($request->user()->id);

        return $this->successResponse(
            TaskResource::collection($tasks),
            'لیست تسک ها با موفقیت دریافت شد'
        );
    }

    #[OA\Post(
        path: "/api/tasks",
        summary: "ساخت تسک جدید",
        description: "ایجاد یک تسک جدید برای پروژه",
        tags: ["Tasks"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["project_id", "title", "status", "priority"],
                properties: [
                    new OA\Property(property: "project_id", type: "integer", example: 1),
                    new OA\Property(property: "version_id", type: "integer", nullable: true, example: 1),
                    new OA\Property(property: "title", type: "string", example: "پیاده‌سازی API لاگین"),
                    new OA\Property(property: "description", type: "string", nullable: true, example: "استفاده از Laravel Sanctum"),
                    new OA\Property(property: "status", type: "string", enum: ["todo", "in_progress", "done"], example: "todo"),
                    new OA\Property(property: "priority", type: "string", enum: ["low", "medium", "high"], example: "high"),
                    new OA\Property(property: "deadline", type: "string", format: "date", nullable: true, example: "2026-10-30"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "تسک با موفقیت ساخته شد",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Task created successfully"),
                        new OA\Property(property: "data", type: "object"),
                    ]
                )
            ),
            new OA\Response(response: 422, description: "خطای اعتبارسنجی"),
        ]
    )]
    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = $this->taskService->createTask($request->user()->id, $request->validated());

        return $this->successResponse(
            new TaskResource($task->load(['project', 'version'])),
            'تسک با موفقیت ساخته شد',
            201
        );
    }

    #[OA\Get(
        path: "/api/tasks/{id}",
        summary: "دریافت یک تسک",
        tags: ["Tasks"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "تسک دریافت شد"),
            new OA\Response(response: 404, description: "تسک یافت نشد"),
        ]
    )]
    public function show(Request $request, Task $task): JsonResponse
    {
        if ($task->user_id !== $request->user()->id) {
            return $this->errorResponse('غیر مجاز', 403);
        }

        return $this->successResponse(
            new TaskResource($task->load(['project', 'version'])),
            'تسک دریافت شد'
        );
    }

    #[OA\Put(
        path: "/api/tasks/{id}",
        summary: "ویرایش تسک",
        tags: ["Tasks"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "title", type: "string"),
                    new OA\Property(property: "status", type: "string", enum: ["todo", "in_progress", "done"]),
                    new OA\Property(property: "priority", type: "string", enum: ["low", "medium", "high"]),
                    new OA\Property(property: "deadline", type: "string", format: "date"),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "تسک ویرایش شد"),
            new OA\Response(response: 422, description: "خطای اعتبارسنجی"),
        ]
    )]
    public function update(StoreTaskRequest $request, Task $task): JsonResponse
    {
        if ($task->user_id !== $request->user()->id) {
            return $this->errorResponse('غیر مجاز', 403);
        }

        $this->taskService->updateTask($task, $request->validated());

        return $this->successResponse(
            new TaskResource($task->fresh(['project', 'version'])),
            'تسک ویرایش شد'
        );
    }

    #[OA\Delete(
        path: "/api/tasks/{id}",
        summary: "حذف تسک",
        tags: ["Tasks"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "تسک حذف شد"),
            new OA\Response(response: 404, description: "تسک یافت نشد"),
        ]
    )]
    public function destroy(Request $request, Task $task): JsonResponse
    {
        if ($task->user_id !== $request->user()->id) {
            return $this->errorResponse('غیر مجاز', 403);
        }

        $this->taskService->deleteTask($task);

        return $this->successResponse(null, 'تسک حذف شد');
    }

    public function stats(Request $request): JsonResponse
    {
        $stats = $this->taskService->getStats($request->user()->id);

        return $this->successResponse($stats, 'آمار تسک با موفقیت ارسال گردید.');
    }

    public function upcoming(Request $request): JsonResponse
    {
        $tasks = $this->taskService->getUpcomingTasks($request->user()->id);
        return $this->successResponse(TaskResource::collection($tasks), 'تسک های نزدیک ارسال گردید.');
    }
}
