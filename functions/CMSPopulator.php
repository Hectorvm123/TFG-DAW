<?php
class CMSPopulator {

    private function populateCMS($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "cms WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."cms WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "cms (
                    `id_cms`, 
                    `id_cms_category`,
                    `position`,
                    `active`,
                    `indexation`
                ) 
                VALUES (
                    '" . pSQL($value['id_cms']) . "', 
                    '" . pSQL($value['id_cms_category']) . "',
                    '" . pSQL($value['position']) . "',
                    '" . pSQL($value['active']) . "',
                    '" . pSQL($value['indexation']) . "'
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


    private function populateCMSLang($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "cms_lang WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."cms_lang WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "cms_lang (
                    `id_cms`, 
                    `id_lang`,
                    `meta_title`,
                    `meta_description`,
                    `meta_keywords`,
                    `content`,
                    `id_shop`,
                    `link_rewrite`
                ) 
                VALUES (
                    '" . pSQL($value['id_cms']) . "', 
                    '" . pSQL($value['id_lang']) . "',
                    '" . pSQL($value['meta_title']) . "',
                    '" . pSQL($value['meta_description']) . "',
                    '" . pSQL($value['meta_keywords']) . "',
                    '" . pSQL($value['content']) . "',
                    '" . pSQL($value['id_shop']) . "',
                    '" . pSQL($value['link_rewrite']) . "'
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

    private function populateCMSShop($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "cms_shop WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."cms_shop WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "cms_shop (
                    `id_cms`, 
                    `id_shop`
                ) 
                VALUES (
                    '" . pSQL($value['id_cms']) . "', 
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
   

    public function populateAllCMS($conn, $prefix) {
        $this->populateCMS($conn,$prefix);
        $this->populateCMSLang($conn,$prefix);
        $this->populateCMSShop($conn,$prefix);
    }
}