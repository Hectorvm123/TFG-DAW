<?php
class AttributePopulator{
    private function populateAttributeGroup($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "attribute_group WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."attribute_group WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "attribute_group (
                    `id_attribute_group`, 
                    `is_color_group`, 
                    `group_type`,
                    `position`
                ) 
                VALUES (
                    '" . pSQL($value['id_attribute_group']) . "', 
                    '" . pSQL($value['is_color_group']) . "', 
                    '" . pSQL($value['group_type']) . "',
                    '" . pSQL($value['position']) . "'
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

    private function populateAttributeGroupLang($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "attribute_group_lang WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."attribute_group_lang WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "attribute_group_lang (
                    `id_attribute_group`, 
                    `id_lang`, 
                    `name`,
                    `public_name`
                ) 
                VALUES (
                    '" . pSQL($value['id_attribute_group']) . "', 
                    '" . pSQL($value['id_lang']) . "', 
                    '" . pSQL($value['name']) . "',
                    '" . pSQL($value['public_name']) . "'
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

    private function populateAttributeGroupShop($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "attribute_group_shop WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."attribute_group_shop WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "attribute_group_shop (
                    `id_attribute_group`, 
                    `id_shop`
                ) 
                VALUES (
                    '" . pSQL($value['id_attribute_group']) . "', 
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

    private function populateLayeredIndexableAttributeGroup($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "layered_indexable_attribute_group WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."layered_indexable_attribute_group WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "layered_indexable_attribute_group (
                    `id_attribute_group`, 
                    `indexable`
                ) 
                VALUES (
                    '" . pSQL($value['id_attribute_group']) . "', 
                    '" . pSQL($value['indexable']) . "'
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

    private function populateLayeredIndexableAttributeGroupLangValue($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "layered_indexable_attribute_group_lang_value WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."layered_indexable_attribute_group_lang_value WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "layered_indexable_attribute_group_lang_value (
                    `id_attribute_group`, 
                    `id_lang`,
                    `url_name`,
                    `meta_title`
                ) 
                VALUES (
                    '" . pSQL($value['id_attribute_group']) . "', 
                    '" . pSQL($value['id_lang']) . "',
                    '" . pSQL($value['url_name']) . "',
                    '" . pSQL($value['meta_title']) . "'
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

    private function populateAttribute($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "attribute WHERE 1");
            $query->execute();

            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "attribute (
                    `id_attribute`, 
                    `id_attribute_group`,
                    `color`,
                    `position`
                ) 
                VALUES (
                    '" . pSQL($value['id_attribute']) . "', 
                    '" . pSQL($value['id_attribute_group']) . "',
                    '" . pSQL($value['color']) . "',
                    '" . pSQL($value['position']) . "'
                )
                ON DUPLICATE KEY UPDATE 
                    id_attribute = VALUES(id_attribute), 
                    id_attribute_group = VALUES(id_attribute_group), 
                    color = VALUES(color), 
                    position = VALUES(position);";
                Db::getInstance()->execute($sql);
            }
        }
        catch(PDOException $exception) {
            echo "Error: " . $exception->getMessage();
        }
        // Cerrar conexion
        $conn = null;
    }

    private function populateAttributeShop($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "attribute_shop WHERE 1");
            $query->execute();

            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "attribute_shop (
                    `id_attribute`, 
                    `id_shop`
                ) 
                VALUES (
                    '" . pSQL($value['id_attribute']) . "', 
                    '" . pSQL($value['id_shop']) . "'
                )
                ON DUPLICATE KEY UPDATE 
                    id_attribute = VALUES(id_attribute), 
                    id_shop = VALUES(id_shop);";
                Db::getInstance()->execute($sql);
            }
        }
        catch(PDOException $exception) {
            echo "Error: " . $exception->getMessage();
        }
        // Cerrar conexion
        $conn = null;
    }

    private function populateLayeredIndexableAttributeLangValue($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "layered_indexable_attribute_lang_value WHERE 1");
            $query->execute();

            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "layered_indexable_attribute_lang_value (
                    `id_attribute`, 
                    `id_lang`,
                    `url_name`,
                    `meta_title`
                ) 
                VALUES (
                    '" . pSQL($value['id_attribute']) . "', 
                    '" . pSQL($value['id_lang']) . "',
                    '" . pSQL($value['url_name']) . "',
                    '" . pSQL($value['meta_title']) . "'
                )
                ON DUPLICATE KEY UPDATE 
                    id_attribute = VALUES(id_attribute), 
                    id_lang = VALUES(id_lang), 
                    url_name = VALUES(url_name), 
                    meta_title = VALUES(meta_title);";
                Db::getInstance()->execute($sql);
            }
        }
        catch(PDOException $exception) {
            echo "Error: " . $exception->getMessage();
        }
        // Cerrar conexion
        $conn = null;
    }

    private function populateAttributeLang($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "attribute_lang WHERE 1");
            $query->execute();

            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "attribute_lang (
                    `id_attribute`, 
                    `id_lang`,
                    `name`
                ) 
                VALUES (
                    '" . pSQL($value['id_attribute']) . "', 
                    '" . pSQL($value['id_lang']) . "',
                    '" . pSQL($value['name']) . "'
                )
                ON DUPLICATE KEY UPDATE 
                    id_attribute = VALUES(id_attribute), 
                    id_lang = VALUES(id_lang), 
                    name = VALUES(name);";
                Db::getInstance()->execute($sql);
            }
        }
        catch(PDOException $exception) {
            echo "Error: " . $exception->getMessage();
        }
        // Cerrar conexion
        $conn = null;
    }

    
    


    public function populateAllAttributes($conn, $prefix) {
        $this->populateAttribute($conn,$prefix);
        $this->populateAttributeShop($conn,$prefix);
        $this->populateAttributeLang($conn,$prefix);
        $this->populateLayeredIndexableAttributeLangValue($conn,$prefix);
        $this->populateAttributeGroup($conn,$prefix);
        $this->populateAttributeGroupLang($conn,$prefix);
        $this->populateAttributeGroupShop($conn,$prefix);
        $this->populateLayeredIndexableAttributeGroup($conn,$prefix);
        $this->populateLayeredIndexableAttributeGroupLangValue($conn,$prefix);
        

       
    }
}