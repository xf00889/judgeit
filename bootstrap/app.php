<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Custom middleware aliases
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
        
        // Note: Laravel 12 automatically includes the following middleware for web routes:
        // - CSRF Protection (VerifyCsrfToken)
        // - Session Management
        // - Cookie Encryption
        // - XSS Protection (via Blade templating)
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Handle business logic exceptions with 422 responses
        $exceptions->render(function (\App\Exceptions\CategoryConfigurationException $e, $request) {
            \Log::error('Category Configuration Error', [
                'message' => $e->getMessage(),
                'user_id' => auth()->id(),
                'url' => $request->fullUrl(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => 'Category configuration error',
                'error' => $e->getMessage()
            ], 422);
        });

        $exceptions->render(function (\App\Exceptions\DuplicateSubmissionException $e, $request) {
            \Log::warning('Duplicate Submission Attempt', [
                'message' => $e->getMessage(),
                'user_id' => auth()->id(),
                'url' => $request->fullUrl()
            ]);
            
            return response()->json([
                'message' => 'Duplicate submission',
                'error' => $e->getMessage()
            ], 422);
        });

        $exceptions->render(function (\App\Exceptions\CategoryNotActiveException $e, $request) {
            \Log::warning('Category Not Active', [
                'message' => $e->getMessage(),
                'user_id' => auth()->id(),
                'url' => $request->fullUrl()
            ]);
            
            return response()->json([
                'message' => 'Category not active',
                'error' => $e->getMessage()
            ], 422);
        });

        // Handle authorization exceptions with 403 responses
        $exceptions->render(function (\App\Exceptions\InsufficientPermissionsException $e, $request) {
            \Log::warning('Insufficient Permissions', [
                'message' => $e->getMessage(),
                'user_id' => auth()->id(),
                'url' => $request->fullUrl()
            ]);
            
            return response()->json([
                'message' => 'Insufficient permissions',
                'error' => $e->getMessage()
            ], 403);
        });

        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, $request) {
            \Log::warning('Authorization Failed', [
                'message' => $e->getMessage(),
                'user_id' => auth()->id(),
                'url' => $request->fullUrl()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'This action is unauthorized',
                    'error' => $e->getMessage()
                ], 403);
            }
            
            return redirect()->back()->with('error', 'You are not authorized to perform this action');
        });

        // Handle validation exceptions with field-specific errors
        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, $request) {
            \Log::info('Validation Failed', [
                'errors' => $e->errors(),
                'user_id' => auth()->id(),
                'url' => $request->fullUrl()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            
            return redirect()->back()->withErrors($e->errors())->withInput();
        });

        // Handle database exceptions with appropriate responses
        $exceptions->render(function (\Illuminate\Database\QueryException $e, $request) {
            \Log::error('Database Query Error', [
                'message' => $e->getMessage(),
                'sql' => $e->getSql() ?? 'N/A',
                'user_id' => auth()->id(),
                'url' => $request->fullUrl(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Check for specific database errors
            $errorCode = $e->errorInfo[1] ?? null;
            
            // Duplicate entry error
            if ($errorCode === 1062) {
                return response()->json([
                    'message' => 'Duplicate entry',
                    'error' => 'This record already exists in the database'
                ], 422);
            }
            
            // Foreign key constraint error
            if ($errorCode === 1451 || $errorCode === 1452) {
                return response()->json([
                    'message' => 'Database constraint violation',
                    'error' => 'This operation violates database integrity constraints'
                ], 422);
            }
            
            // Generic database error
            return response()->json([
                'message' => 'Database error',
                'error' => 'An error occurred while processing your request'
            ], 500);
        });

        // Handle CSRF token mismatch exceptions
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            \Log::warning('CSRF Token Mismatch', [
                'message' => $e->getMessage(),
                'user_id' => auth()->id(),
                'url' => $request->fullUrl()
            ]);
            
            // If it's a logout request, just redirect to login
            if ($request->is('logout')) {
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect('/login')->with('message', 'You have been logged out.');
            }
            
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'CSRF token mismatch',
                    'error' => 'Your session has expired. Please refresh the page and try again.'
                ], 419);
            }
            
            // For login page, redirect to login to get fresh token
            if ($request->is('login')) {
                return redirect()->route('login')->with('error', 'Your session has expired. Please try again.');
            }
            
            return redirect()->back()->with('error', 'Your session has expired. Please try again.');
        });

        // Handle general exceptions
        $exceptions->render(function (\Throwable $e, $request) {
            \Log::error('Unhandled Exception', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'user_id' => auth()->id(),
                'url' => $request->fullUrl(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Let Laravel handle the rendering based on debug mode
            // When debug=true, it will show detailed error page
            // When debug=false, it will show generic error page
        });
    })->create();
