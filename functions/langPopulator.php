<?php
class LangPopulator {
    public function populateLang($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "lang WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."lang WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "lang (
                    `id_lang`, 
                    `name`,
                    `active`,
                    `iso_code`,
                    `language_code`,
                    `date_format_lite`,
                    `date_format_full`,
                    `is_rtl`,
                    `locale`
                ) 
                VALUES (
                    '" . pSQL($value['id_lang']) . "', 
                    '" . pSQL($value['name']) . "',
                    '" . pSQL($value['active']) . "',
                    '" . pSQL($value['iso_code']) . "',
                    '" . pSQL($value['language_code']) . "',
                    '" . pSQL($value['date_format_lite']) . "',
                    '" . pSQL($value['date_format_full']) . "',
                    '" . pSQL($value['is_rtl']) . "', 
                    '" . pSQL($value['iso_code']) . "'
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

    public function populateLangShop($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "lang_shop WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."lang_shop WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "lang_shop (
                    `id_lang`,
                    `id_shop`
                ) 
                VALUES (
                    '" . pSQL($value['id_lang']) . "',
                    '" . pSQL($value['id_shop']) . "'
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

    
    

    public function populateAllLangs($conn, $prefix) {
        $this->populateLang($conn,$prefix);
        $this->populateLangShop($conn,$prefix);
        
    }
}