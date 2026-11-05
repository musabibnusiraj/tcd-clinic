<?php

/**
 * Project Name: Tooth Care - Channeling Appointments
 * Author: Musab Ibn Siraj
 */

/**
 * ImageUploader - validates and saves an uploaded image file.
 *
 * Usage:
 *   $uploader = new ImageUploader(BASE_PATH . '/assets/uploads/');
 *   $fileName = $uploader->save($_FILES['image'] ?? null);   // null when no file was chosen
 *
 * Throws an InvalidArgumentException with a user-friendly message when the
 * file is not valid (the caller shows that message to the user).
 */
class ImageUploader
{
    /** Maximum allowed file size in bytes (500 KB) */
    const MAX_SIZE = 500000;

    /** File extensions that are allowed */
    const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    /** @var string Folder where files are stored (must end with a slash) */
    private $targetDir;

    /**
     * @param string $targetDir Folder where uploaded images are saved
     */
    public function __construct($targetDir)
    {
        $this->targetDir = $targetDir;
    }

    /**
     * Validates and moves the uploaded file into the target folder.
     *
     * @param array|null $file One entry of $_FILES
     * @return string|null The new file name, or null when no file was uploaded
     * @throws InvalidArgumentException When the file is invalid
     */
    public function save($file)
    {
        // No file chosen by the user: nothing to do
        if (empty($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new InvalidArgumentException('Error uploading file: ' . $file['error']);
        }

        // Must be a real image
        if (getimagesize($file['tmp_name']) === false) {
            throw new InvalidArgumentException('Uploaded file is not an image.');
        }

        if ($file['size'] >= self::MAX_SIZE) {
            throw new InvalidArgumentException('File size is too large.');
        }

        // Only allow known image extensions (never trust the file name blindly)
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
            throw new InvalidArgumentException('Only JPG, PNG, GIF or WEBP images are allowed.');
        }

        // Use a unique name so files never overwrite each other
        $newFileName = uniqid() . '.' . $extension;

        if (!move_uploaded_file($file['tmp_name'], $this->targetDir . $newFileName)) {
            throw new InvalidArgumentException('Error moving uploaded file.');
        }

        return $newFileName;
    }
}
