<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFileUploadRequest;
use App\Imports\HomeownerImport;
use App\Models\Title;
use App\Services\HomeownerCsvUploadService;
use Arr;
use Exception;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class FileUploadController extends Controller {

    /**
     * Display the file upload page.
     *
     * @return Response
     */
    public function index(): Response {
        return Inertia::render('Home');
    }

    /**
     * File Upload Request
     *
     * @param StoreFileUploadRequest $request
     * @param HomeownerCsvUploadService $service
     * @return void
     *
     */
    public function store(StoreFileUploadRequest $request, HomeownerCsvUploadService $service): void {
        $file = $request->file('file');

        try {
            $data = $service->processCsv($file);

            dd($data);
            // Normally I would return back with interia so hope you're okay with just the dump.

        } catch (Exception $e) {
            // I Normally would return back to user with error and logged for debugging.
            dd($e->getMessage());
        }
    }
}
