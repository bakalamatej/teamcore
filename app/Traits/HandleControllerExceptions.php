<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

trait HandleControllerExceptions
{
    /**
     * Execute and return view response with exception handling
     *
     * @param callable $action
     * @return mixed
     */
    protected function handleView(callable $action)
    {
        try {
            return $action();
        } catch (AuthorizationException $e) {
            Log::warning("Authorization denied: {$e->getMessage()}");
            abort(403, 'You are not authorized to perform this action.');
        } catch (ModelNotFoundException $e) {
            Log::warning("Model not found: {$e->getMessage()}");
            abort(404, 'The requested resource was not found.');
        } catch (\Exception $e) {
            Log::error("View error: " . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Execute and return redirect response with exception handling
     *
     * @param callable $action
     * @return mixed
     */
    protected function handleRedirect(callable $action)
    {
        try {
            return $action();
        } catch (AuthorizationException $e) {
            Log::warning("Authorization denied: {$e->getMessage()}");
            abort(403, 'You are not authorized to perform this action.');
        } catch (ModelNotFoundException $e) {
            Log::warning("Model not found: {$e->getMessage()}");
            abort(404, 'The requested resource was not found.');
        } catch (\Exception $e) {
            Log::error("Action error: " . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Validate sort parameter against whitelist of allowed columns
     * Prevents SQL injection in ORDER BY clause
     *
     * @param string $sort
     * @param string $order
     * @param array $allowedColumns
     * @return void (applies order by directly on query context)
     */
    protected function validateSortParams(string $sort, string $order, array $allowedColumns): void
    {
        // Validate order direction
        $validOrder = in_array(strtolower($order), ['asc', 'desc'], true) ? strtolower($order) : 'desc';
        
        // Validate sort column against whitelist
        if (!in_array($sort, $allowedColumns, true)) {
            abort(400, 'Invalid sort column: ' . $sort);
        }
    }

    /**
     * Cache a model query result with TTL
     * Default TTL: 1 hour (3600 seconds)
     *
     * @param string $cacheKey
     * @param callable $callback
     * @param int $ttl - Time to live in seconds
     * @return mixed
     */
    protected function rememberModelQuery(string $cacheKey, callable $callback, int $ttl = 3600)
    {
        return Cache::remember($cacheKey, $ttl, $callback);
    }
}
