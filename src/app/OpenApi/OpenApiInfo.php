<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "DeadlineFlow API",
    description: "API مستندات سیستم مدیریت ددلاین و تسک ها",
    contact: new OA\Contact(email: "demo@deadlineflow.ir")
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    bearerFormat: "JWT"
)]
class OpenApiInfo
{
    
}