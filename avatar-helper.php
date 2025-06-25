<?php
// avatar-helper.php - Include în toate paginile cu avatare

/**
 * Returnează URL-ul avatarului utilizatorului
 * @param int $user_id - ID-ul utilizatorului
 * @param string|null $avatar_filename - Numele fișierului avatar (din BD)
 * @return string - URL-ul avatarului
 */
function getAvatarUrl($user_id, $avatar_filename = null) {
    if ($avatar_filename && file_exists("uploads/avatars/" . $avatar_filename)) {
        return "uploads/avatars/" . $avatar_filename;
    }
    // Default avatar bazat pe ID (serviciu extern)
    return "https://ui-avatars.com/api/?name=" . urlencode("User" . $user_id) . "&background=007bff&color=fff&size=150";
}

/**
 * Afișează HTML pentru avatar (imagine sau inițiale)
 * @param int $user_id - ID-ul utilizatorului
 * @param string $username - Username-ul utilizatorului
 * @param string|null $avatar_filename - Numele fișierului avatar
 * @param int $size - Dimensiunea avatarului în pixeli
 * @return string - HTML pentru avatar
 */
function displayAvatar($user_id, $username, $avatar_filename = null, $size = 40) {
    $avatar_url = getAvatarUrl($user_id, $avatar_filename);
    $initials = strtoupper(substr($username, 0, 2));
    
    if (strpos($avatar_url, 'ui-avatars.com') !== false) {
        // Avatar generat - afișează inițialele local
        return "<div class='bg-primary text-white rounded-circle d-flex align-items-center justify-content-center' style='width: {$size}px; height: {$size}px; font-size: " . ($size/2.5) . "px;'>{$initials}</div>";
    } else {
        // Avatar uploadat - afișează imaginea
        return "<img src='{$avatar_url}' alt='{$username}' class='rounded-circle' style='width: {$size}px; height: {$size}px; object-fit: cover;'>";
    }
}

/**
 * Procesează upload-ul unui avatar
 * @param int $user_id - ID-ul utilizatorului
 * @param array $file - $_FILES['avatar']
 * @return string - Numele fișierului salvat
 * @throws Exception - În caz de eroare
 */
function handleAvatarUpload($user_id, $file) {
    $upload_dir = "uploads/avatars/";
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    $max_size = 2 * 1024 * 1024; // 2MB
    
    // Verifică dacă directorul există, dacă nu îl creează
    if (!file_exists($upload_dir)) {
        if (!mkdir($upload_dir, 0755, true)) {
            throw new Exception("Nu s-a putut crea directorul pentru avatare!");
        }
    }
    
    // Validări
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("Eroare la upload: " . getUploadError($file['error']));
    }
    
    if ($file['size'] > $max_size) {
        throw new Exception("Fișierul este prea mare! Mărimea maximă: 2MB");
    }
    
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($file_extension, $allowed_types)) {
        throw new Exception("Tipul fișierului nu este permis! Folosește: " . implode(', ', $allowed_types));
    }
    
    // Verifică dacă este imagine
    $image_info = getimagesize($file['tmp_name']);
    if ($image_info === false) {
        throw new Exception("Fișierul nu este o imagine validă!");
    }
    
    // Generează nume unic
    $new_filename = "avatar_" . $user_id . "_" . time() . "." . $file_extension;
    $upload_path = $upload_dir . $new_filename;
    
    // Mută fișierul
    if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
        throw new Exception("Eroare la salvarea fișierului!");
    }
    
    return $new_filename;
}

/**
 * Șterge un avatar vechi
 * @param string $avatar_filename - Numele fișierului de șters
 */
function deleteOldAvatar($avatar_filename) {
    if ($avatar_filename && file_exists("uploads/avatars/" . $avatar_filename)) {
        unlink("uploads/avatars/" . $avatar_filename);
    }
}

/**
 * Returnează mesajul de eroare pentru codurile de upload
 * @param int $error_code - Codul de eroare din $_FILES
 * @return string - Mesajul de eroare
 */
function getUploadError($error_code) {
    switch ($error_code) {
        case UPLOAD_ERR_INI_SIZE:
            return "Fișierul depășește upload_max_filesize din php.ini";
        case UPLOAD_ERR_FORM_SIZE:
            return "Fișierul depășește MAX_FILE_SIZE specificat în formular";
        case UPLOAD_ERR_PARTIAL:
            return "Fișierul a fost uploadat doar parțial";
        case UPLOAD_ERR_NO_FILE:
            return "Nu a fost uploadat niciun fișier";
        case UPLOAD_ERR_NO_TMP_DIR:
            return "Lipsește directorul temporar";
        case UPLOAD_ERR_CANT_WRITE:
            return "Nu s-a putut scrie fișierul pe disk";
        case UPLOAD_ERR_EXTENSION:
            return "Upload oprit de o extensie PHP";
        default:
            return "Eroare necunoscută la upload";
    }
}

/**
 * Verifică și creează directoarele necesare pentru avatare
 * @return bool - True dacă directoarele există sau au fost create cu succes
 */
function ensureAvatarDirectories() {
    $directories = ['uploads', 'uploads/avatars'];
    
    foreach ($directories as $dir) {
        if (!file_exists($dir)) {
            if (!mkdir($dir, 0755, true)) {
                return false;
            }
        }
    }
    
    return true;
}

/**
 * Returnează informații despre un avatar
 * @param string $avatar_filename - Numele fișierului avatar
 * @return array|null - Informații despre fișier sau null dacă nu există
 */
function getAvatarInfo($avatar_filename) {
    if (!$avatar_filename) {
        return null;
    }
    
    $file_path = "uploads/avatars/" . $avatar_filename;
    
    if (!file_exists($file_path)) {
        return null;
    }
    
    return [
        'filename' => $avatar_filename,
        'path' => $file_path,
        'size' => filesize($file_path),
        'size_formatted' => formatBytes(filesize($file_path)),
        'modified' => filemtime($file_path),
        'modified_formatted' => date('d.m.Y H:i', filemtime($file_path))
    ];
}

/**
 * Formatează dimensiunea fișierului
 * @param int $size - Dimensiunea în bytes
 * @return string - Dimensiunea formatată
 */
function formatBytes($size) {
    $units = ['B', 'KB', 'MB', 'GB'];
    
    for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
        $size /= 1024;
    }
    
    return round($size, 2) . ' ' . $units[$i];
}
?>