<?php

return [
    'default_from_email' => 'no-reply@widefrontpack.ro',
    'default_to_email' => 'office@widefrontpack.ro',
    'files' => [
        'avatar_upload_path' => storage_path().'/uploads/avatars',
        'company_path' => '/assets/company/',
        'uploaded_file_permissions' => 0755,
        'allowed_types' => ['png', 'jpg', 'jpeg'],
        'max_zip_files_allowed' => 5,
        'resize_width' => 250,
        'resize_height' => 50,
    ],
];
