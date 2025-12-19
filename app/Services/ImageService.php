<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Service pour gérer l'upload et la conversion d'images.
 */
class ImageService
{
    /**
     * Upload une image et retourne l'URL publique.
     */
    public function uploadImage(UploadedFile $file): string
    {
        // Valider le type MIME
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file->getMimeType(), $allowedMimes, true)) {
            throw new \InvalidArgumentException('Type de fichier non supporté. Utilisez JPEG, PNG, GIF ou WebP.');
        }

        // Valider la taille (max 5MB)
        if ($file->getSize() > 5 * 1024 * 1024) {
            throw new \InvalidArgumentException('L\'image ne doit pas dépasser 5 MB.');
        }

        // Stocker dans public/images
        $path = $file->store('images', 'public');

        return Storage::url($path);
    }

    /**
     * Convertir une image en base64 pour l'API OpenRouter.
     */
    public function imageToBase64(UploadedFile $file): string
    {
        $imageData = file_get_contents($file->getRealPath());
        $base64 = base64_encode($imageData);
        $mimeType = $file->getMimeType();

        return "data:{$mimeType};base64,{$base64}";
    }

    /**
     * Supprimer une image du storage.
     */
    public function deleteImage(string $url): bool
    {
        // Extraire le chemin depuis l'URL
        $path = str_replace('/storage/', '', parse_url($url, PHP_URL_PATH));

        return Storage::disk('public')->delete($path);
    }
}
