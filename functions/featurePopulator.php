<?php
class FeaturePopulator {
    private function populateFeature($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "feature WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."feature WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "feature (
                    `id_feature`, 
                    `position`
                ) 
                VALUES (
                    '" . pSQL($value['id_feature']) . "', 
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

    private function populateFeatureFlag($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "feature_flag WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."feature_flag WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "feature_flag (
                    `id_feature_flag`, 
                    `state`, 
                    `name`,
                    `label_wording`,
                    `label_domain`,
                    `description_wording`,
                    `description_domain`,
                    `stability`
                ) 
                VALUES (
                    '" . pSQL($value['id_feature_flag']) . "', 
                    '" . pSQL($value['state']) . "', 
                    '" . pSQL($value['name']) . "',
                    '" . pSQL($value['label_wording']) . "',
                    '" . pSQL($value['label_domain']) . "',
                    '" . pSQL($value['description_wording']) . "',
                    '" . pSQL($value['description_domain']) . "',
                    '" . pSQL($value['stability']) . "'
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

    private function populateFeatureLang($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "feature_lang WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."feature_lang WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "feature_lang (
                    `id_feature`, 
                    `id_lang`,
                    `name`
                ) 
                VALUES (
                    '" . pSQL($value['id_feature']) . "', 
                    '" . pSQL($value['id_lang']) . "',
                    '" . pSQL($value['name']) . "'
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

    

    private function populateFeatureShop($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "feature_shop WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."feature_shop WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "feature_shop (
                    `id_feature`, 
                    `id_shop`
                ) 
                VALUES (
                    '" . pSQL($value['id_feature']) . "', 
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

    private function populateFeatureValue($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "feature_value WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."feature_value WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "feature_value (
                    `id_feature_value`, 
                    `id_feature`,
                    `custom`
                ) 
                VALUES (
                    '" . pSQL($value['id_feature_value']) . "', 
                    '" . pSQL($value['id_feature']) . "',
                    '" . pSQL($value['custom']) . "'
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

    private function populateFeatureValueLang($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "feature_value_lang WHERE 1");
            $query->execute();

            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "feature_value_lang (
                    `id_feature_value`, 
                    `id_lang`,
                    `value`
                ) 
                VALUES (
                    '" . pSQL($value['id_feature_value']) . "', 
                    '" . pSQL($value['id_lang']) . "',
                    '" . pSQL($value['value']) . "'
                )
                ON DUPLICATE KEY UPDATE 
                    id_feature_value = VALUES(id_feature_value), 
                    id_lang = VALUES(id_lang), 
                    value = VALUES(value);";
                Db::getInstance()->execute($sql);
            }
        }
        catch(PDOException $exception) {
            echo "Error: " . $exception->getMessage();
        }
        // Cerrar conexion
        $conn = null;
    }

    private function populateLayeredIndexableFeature($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "layered_indexable_feature WHERE 1");
            $query->execute();

            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "layered_indexable_feature (
                    `id_feature`, 
                    `indexable`
                ) 
                VALUES (
                    '" . pSQL($value['id_feature']) . "', 
                    '" . pSQL($value['indexable']) . "'
                )
                ON DUPLICATE KEY UPDATE 
                    id_feature = VALUES(id_feature), 
                    indexable = VALUES(indexable);";
                Db::getInstance()->execute($sql);
            }
        }
        catch(PDOException $exception) {
            echo "Error: " . $exception->getMessage();
        }
        // Cerrar conexion
        $conn = null;
    }

    private function populateLayeredIndexableFeatureLangValue($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "layered_indexable_feature_lang_value WHERE 1");
            $query->execute();

            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "layered_indexable_feature_lang_value (
                    `id_feature`, 
                    `id_lang`,
                    `url_name`,
                    `meta_title`
                ) 
                VALUES (
                    '" . pSQL($value['id_feature']) . "', 
                    '" . pSQL($value['id_lang']) . "',
                    '" . pSQL($value['url_name']) . "',
                    '" . pSQL($value['meta_title']) . "'
                )
                ON DUPLICATE KEY UPDATE 
                    id_feature = VALUES(id_feature), 
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

    private function populateLayeredIndexableFeatureValueLangValue($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "layered_indexable_feature_value_lang_value WHERE 1");
            $query->execute();

            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "layered_indexable_feature_value_lang_value (
                    `id_feature_value`, 
                    `id_lang`,
                    `url_name`,
                    `meta_title`
                ) 
                VALUES (
                    '" . pSQL($value['id_feature_value']) . "', 
                    '" . pSQL($value['id_lang']) . "',
                    '" . pSQL($value['url_name']) . "',
                    '" . pSQL($value['meta_title']) . "'
                )
                ON DUPLICATE KEY UPDATE 
                    id_feature_value = VALUES(id_feature_value), 
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

    
    


    public function populateAllFeatures($conn, $prefix) {
        $this->populateFeature($conn,$prefix);
        //$this->populateFeatureFlag($conn,$prefix);
        $this->populateFeatureLang($conn,$prefix);
        $this->populateFeatureShop($conn,$prefix);
        $this->populateFeatureValue($conn,$prefix);
        $this->populateFeatureValueLang($conn,$prefix);
        $this->populateLayeredIndexableFeature($conn,$prefix);
        $this->populateLayeredIndexableFeatureLangValue($conn,$prefix);
        $this->populateLayeredIndexableFeatureValueLangValue($conn,$prefix);
        

       
    }
}