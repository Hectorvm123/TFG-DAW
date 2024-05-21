<?php
class ImagePopulator {

    private function populateImage($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "image WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."image WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "image (
                    `id_image`, 
                    `id_product`,
                    `position`,
                    `cover`
                ) 
                VALUES (
                    '" . pSQL($value['id_image']) . "', 
                    '" . pSQL($value['id_product']) . "',
                    '" . pSQL($value['position']) . "',
                    NULL
                )";
                Db::getInstance()->execute($sql);
            }
        }
        catch(PDOException $exception) {
            echo "Error: " . $exception->getMessage();
        }
        // Cerrar conexion
        $conn = null;
    }

    private function populateImageLang($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "image_lang WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."image_lang WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "image_lang (
                    `id_image`, 
                    `id_lang`,
                    `legend`
                ) 
                VALUES (
                    '" . pSQL($value['id_image']) . "', 
                    '" . pSQL($value['id_lang']) . "',
                    '" . pSQL($value['legend']) . "'
                )";
                Db::getInstance()->execute($sql);
            }
        }
        catch(PDOException $exception) {
            echo "Error: " . $exception->getMessage();
        }
        // Cerrar conexion
        $conn = null;
    }

    private function populateImageShop($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "image_shop WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."image_shop WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "image_shop (
                    `id_image`, 
                    `id_product`,
                    `id_shop`,
                    `cover`
                ) 
                VALUES (
                    '" . pSQL($value['id_image']) . "', 
                    '" . pSQL($value['id_product']) . "',
                    '" . pSQL($value['id_shop']) . "',
                    NULL
                )";
                Db::getInstance()->execute($sql);
            }
        }
        catch(PDOException $exception) {
            echo "Error: " . $exception->getMessage();
        }
        // Cerrar conexion
        $conn = null;
    }

    private function populateImageType($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "image_type WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."image_type WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "image_type (
                    `id_image_type`, 
                    `name`,
                    `width`,
                    `height`,
                    `products`,
                    `categories`,
                    `suppliers`,
                    `stores`,
                    `manufacturers`
                ) 
                VALUES (
                    '" . pSQL($value['id_image_type']) . "', 
                    '" . pSQL($value['name']) . "',
                    '" . pSQL($value['width']) . "',
                    '" . pSQL($value['height']) . "',
                    '" . pSQL($value['products']) . "',
                    '" . pSQL($value['categories']) . "',
                    '" . pSQL($value['suppliers']) . "',
                    '" . pSQL($value['stores']) . "',
                    '" . pSQL($value['manufacturers']) . "'
                )";
                Db::getInstance()->execute($sql);
            }
        }
        catch(PDOException $exception) {
            echo "Error: " . $exception->getMessage();
        }
        // Cerrar conexion
        $conn = null;
    }
    
   

    public function populateAllImages($conn, $prefix) {
        $this->populateImage($conn,$prefix);
        $this->populateImageLang($conn,$prefix);
        $this->populateImageShop($conn,$prefix);
        $this->populateImageType($conn,$prefix);
    }
}