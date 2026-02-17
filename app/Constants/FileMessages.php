<?php

namespace App\Constants;

class FileMessages
{
    // Upload validation errors
    const FILE_REQUIRED = 'Please select a file to upload.';
    const FILE_INVALID = 'The upload is not a valid file.';
    const FILE_TOO_LARGE = 'The file is too large.';
    const FILE_TYPE_NOT_ALLOWED = 'The file type is not allowed.';
    const CATEGORY_REQUIRED = 'Please select a file category.';
    const CATEGORY_INVALID = 'The selected category is not valid.';
    
    // Service validation errors
    const CATEGORY_NOT_ALLOWED = 'Category is not allowed.';
    
    // Model errors
    const MODEL_NOT_FOUND = 'Model not found.';
    const FILE_NOT_FOUND = 'File not found.';
    
    // File operation errors
    const UPLOAD_ERROR = 'Error uploading file: ';
    const LIST_ERROR = 'Error retrieving files: ';
    const DELETE_ERROR = 'Error deleting file: ';
    
    // Success messages
    const FILE_UPLOADED = 'File uploaded successfully.';
    const FILE_DELETED = 'File deleted successfully.';
}
