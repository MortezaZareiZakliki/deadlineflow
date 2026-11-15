<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVersionRequest;
use App\Http\Resources\VersionResource;
use App\Models\Project;
use App\Models\Version;
use App\Services\VersionService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use Exception;

class VersionController extends Controller
{
    use ApiResponse;

    public function __construct(protected VersionService $versionService) {}

    #[OA\Get(
        path: "/api/projects/{project}/versions",
        summary: "لیست نسخه های یک پروژه",
        tags: ["Versions"],
        security: [["bearerAuth" => []]],
        parameters: [new OA\Parameter(name: "project", in: "path", required: true, schema: new OA\Schema(type: "integer"))],
        responses: [new OA\Response(response: 200, description: "موفق")]
    )]
    public function index(Request $request, Project $project): JsonResponse
    {
        if ($project->user_id !== $request->user()->id) {
            return $this->errorResponse('غیر مجاز', 403);
        }

        $versions = $this->versionService->listVersions($project->id);
        $versions->loadCount('tasks');
        
        return $this->successResponse(VersionResource::collection($versions), 'لیست نسخه های یک پروژه با موفقیت ارسال گردید.');
    }

    #[OA\Post(
        path: "/api/projects/{project}/versions",
        summary: "ساخت نسخه جدید",
        tags: ["Versions"],
        security: [["bearerAuth" => []]],
        parameters: [new OA\Parameter(name: "project", in: "path", required: true, schema: new OA\Schema(type: "integer"))],
        responses: [new OA\Response(response: 201, description: "ساخته شد")]
    )]
    public function store(StoreVersionRequest $request, Project $project): JsonResponse
    {
        if ($project->user_id !== $request->user()->id) {
            return $this->errorResponse('غیر مجاز', 403);
        }

        $version = $this->versionService->createVersion($project->id, $request->validated());
        
        return $this->successResponse(new VersionResource($version), 'نسخه ساخته شد', 201);
    }

    #[OA\Delete(
        path: "/api/versions/{version}",
        summary: "حذف نسخه",
        tags: ["Versions"],
        security: [["bearerAuth" => []]],
        parameters: [new OA\Parameter(name: "version", in: "path", required: true, schema: new OA\Schema(type: "integer"))],
        responses: [
            new OA\Response(response: 200, description: "حذف شد"),
            new OA\Response(response: 422, description: "نسخه دارای تسک است")
        ]
    )]
    public function destroy(Request $request, Version $version): JsonResponse
    {
        if ($version->project->user_id !== $request->user()->id) {
            return $this->errorResponse('غیر مجاز', 403);
        }

        try {
            $this->versionService->deleteVersion($version);
            return $this->successResponse(null, 'نسخه با موفقیت حذف گردید.');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }
}