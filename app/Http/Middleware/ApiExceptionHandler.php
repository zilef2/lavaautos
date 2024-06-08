<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class ApiExceptionHandler
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            return $next($request);
        } catch (ValidationException $e) {
            throw new HttpResponseException(response()->json([
                'errors' => $e->errors()
            ], 422));
        } catch (HttpResponseException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getStatusCode());
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Server Error',
                'error' => $e->getMessage() . ' - '. $e->getCode() . ' - ' . $e->getFile() . ' - ' . $e->getLine()
            ], 500);
        }
    }
}
