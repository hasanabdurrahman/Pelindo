<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait FileUploadTrait
{
    /**
     * handleFileUpload
     *
     * @param  mixed $request
     * @param  mixed $field
     * @param  mixed $storagePath
     * @return void
     */
    public function handleFileUpload($request, $field, $storagePath)
    {
        if ($request->hasFile($field)) {
            $uploadedFile = $request->file($field);

            // Generate a unique filename
            $extension = $uploadedFile->getClientOriginalExtension();
            $fileName = uniqid() . '_' . Str::random(10) . '.' . $extension;

            // Store the file with the unique filename
            $uploadedFile->storeAs('public/'.$storagePath, $fileName);

            return $fileName;
        }
    }
}
