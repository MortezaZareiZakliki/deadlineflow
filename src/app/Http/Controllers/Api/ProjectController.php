<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Services\ProjectService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ProjectController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected ProjectService $projectService
    ) {}

    #[OA\Get(
        path: "/api/projects",
        summary: "لیست پروژه ها",
        description: "دریافت لیست تمام پروژه ها",
        tags: ["Projects"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "لیست پروژه ها با موفقیت دریافت شد",
            ),
            new OA\Response(response: 401, description: "عدم احراز هویت"),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $tasks = $this->projectService->listProjects($request->user()->id);
        
        return $this->successResponse(
            ProjectResource::collection($tasks),
            'لیست پروژه ها با موفقیت دریافت شد'
        );
    }

    #[OA\Post(
        path: "/api/projects",
        summary: "ساخت پروژه جدید",
        description: "ایجاد یک پروژه جدید",
        tags: ["Projects"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "priority"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "پروژه فروشگاه"),
                    new OA\Property(property: "priority", type: "string", enum: ["low", "medium", "high"], example: "high"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "پروژه با موفقیت ساخته شد",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Project created successfully"),
                        new OA\Property(property: "data", type: "object"),
                    ]
                )
            ),
            new OA\Response(response: 422, description: "خطای اعتبارسنجی"),
        ]
    )]
    public function store(StoreProjectRequest $request): JsonResponse
    {
        $project = $this->projectService->createProject($request->user()->id, $request->validated());
        
        return $this->successResponse(
            new ProjectResource($project),
            'پروژه با موفقیت ساخته شد',
            201
        );
    }

    #[OA\Put(
        path: "/api/projects/{id}",
        summary: "ویرایش پروژه",
        tags: ["Projects"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "name", type: "string"),
                    new OA\Property(property: "priority", type: "string", enum: ["low", "medium", "high"]),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "پروژه ویرایش شد"),
            new OA\Response(response: 422, description: "خطای اعتبارسنجی"),
        ]
    )]
    public function update(StoreProjectRequest $request, Project $project): JsonResponse
    {
        if($project->user_id !== $request->user()->id) {
            return $this->errorResponse('غیر مجاز', 403);
        }

        $this->projectService->updateProject($project, $request->validated());
        
        return $this->successResponse(
            new ProjectResource($project->fresh()),
            'پروژه ویرایش شد'
        );
    }

    #[OA\Delete(
        path: "/api/projects/{id}",
        summary: "حذف پروژه",
        tags: ["Project"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "پروژه حذف شد"),
            new OA\Response(response: 404, description: "پروژه یافت نشد"),
        ]
    )]
    public function destroy(Request $request, Project $project): JsonResponse
    {
        if($project->user_id !== $request->user()->id) {
            return $this->errorResponse('غیر مجاز', 403);
        }

        $this->projectService->deleteProject($project);
        
        return $this->successResponse(null, 'پروژه حذف شد');
    }
}