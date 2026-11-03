<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Spatie\Health\ResultStores\ResultStore;

/**
 * Public liveness/health endpoint for uptime monitors and load balancers.
 *
 * Returns only {"healthy": true|false} with HTTP 200 / 503, based on the
 * results stored by the scheduled `health:check` run. It never runs checks
 * itself (so it cannot be abused to load the server) and never exposes check
 * names, messages or configuration — detailed results are on the
 * permission-protected System status screen.
 */
class HealthController extends Controller
{
    public function __invoke(ResultStore $results): JsonResponse
    {
        $healthy = (bool) $results->latestResults()?->allChecksOk();

        return response()
            ->json(['healthy' => $healthy], $healthy ? 200 : 503)
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }
}
