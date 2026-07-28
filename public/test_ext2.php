<?php echo json_encode(["dir" => ini_get("extension_dir"), "mb" => extension_loaded("mbstring"), "time" => time()]); ?>
