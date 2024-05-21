<?php
class CategoryPopulator{
    

    private function populateCategory($conn, $prefix){
        try {
                $query = $conn->prepare("SELECT * FROM " .$prefix. "category WHERE 1");
                $query->execute();

                Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."category WHERE 1;");
                foreach($query->fetchAll() as $key=>$value) {
                    if($value['id_category'] != 1 || $value['id_category'] != 2){

                    
                    $sql = "INSERT INTO " . _DB_PREFIX_ . "category (
                        `id_category`,
                        `id_parent`,
                        `id_shop_default`,
                        `level_depth`,
                        `nleft`,
                        `nright`,
                        `active`,
                        `date_add`,
                        `date_upd`,
                        `position`,
                        `is_root_category`
                    ) 
                    VALUES (
                        '" . pSQL($value['id_category']) . "',
                        '" . pSQL($value['id_parent']) . "',
                        '" . pSQL($value['id_shop_default']) . "',
                        '" . pSQL($value['level_depth']) . "',
                        '" . pSQL($value['nleft']) . "',
                        '" . pSQL($value['nright']) . "',
                        '" . pSQL($value['active']) . "',
                        '" . pSQL($value['date_add']) . "',
                        '" . pSQL($value['date_upd']) . "',
                        '" . pSQL($value['position']) . "',
                        '" . pSQL($value['is_root_category']) . "'
                    )
                    ON DUPLICATE KEY UPDATE 
                    id_parent = VALUES(id_parent), 
                    id_shop_default = VALUES(id_shop_default), 
                    level_depth = VALUES(level_depth), 
                    nleft = VALUES(nleft), 
                    nright = VALUES(nright), 
                    active = VALUES(active), 
                    date_add = VALUES(date_add), 
                    date_upd = VALUES(date_upd), 
                    position = VALUES(position), 
                    is_root_category = VALUES(is_root_category);";
                    Db::getInstance()->execute($sql);
                }
            }
        }

        catch(PDOException $exception) {
            echo "Error: " . $exception->getMessage();
        }
        // Cerrar conexion
        $conn = null;
    }

    private function populateCategoryGroup($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "category_group WHERE 1");
            $query->execute();

            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO ". _DB_PREFIX_ ."category_group (`id_category`, `id_group`) VALUES ('" . pSQL($value['id_category']) . "', '" . pSQL($value['id_group']) . "')
                ON DUPLICATE KEY UPDATE 
                id_group = VALUES(id_group);";
                Db::getInstance()->execute($sql);
            }
        }
        catch(PDOException $exception) {
            echo "Error: " . $exception->getMessage();
        }
        // Cerrar conexion
        $conn = null;
    }
    
    private function populateCategoryLang($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "category_lang WHERE 1");
            $query->execute();

            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO ". _DB_PREFIX_ ."category_lang (
                    `id_category`, 
                    `id_shop`,
                    `id_lang`,
                    `name`,
                    `description`,
                    `link_rewrite`,
                    `meta_title`,
                    `meta_keywords`,
                    `meta_description`
                    ) VALUES (
                        '" . pSQL($value['id_category']) . "', 
                        '" . pSQL($value['id_shop']) . "',
                        '" . pSQL($value['id_lang']) . "',
                        '" . pSQL($value['name']) . "',
                        '" . pSQL($value['description']) . "',
                        '" . pSQL($value['link_rewrite']) . "',
                        '" . pSQL($value['meta_title']) . "',
                        '" . pSQL($value['meta_keywords']) . "',
                        '" . pSQL($value['meta_description']) . "'
                        )
                        ON DUPLICATE KEY UPDATE 
                    id_shop = VALUES(id_shop), 
                    id_lang = VALUES(id_lang), 
                    name = VALUES(name), 
                    description = VALUES(description), 
                    link_rewrite = VALUES(link_rewrite), 
                    meta_title = VALUES(meta_title), 
                    meta_keywords = VALUES(meta_keywords), 
                    meta_description = VALUES(meta_description);";
                Db::getInstance()->execute($sql);
            }
        }
        catch(PDOException $exception) {
            echo "Error: " . $exception->getMessage();
        }
        // Cerrar conexion
        $conn = null;
    }

    private function populateCategoryProduct($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "category_product WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."category_product WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO ". _DB_PREFIX_ ."category_product (
                    `id_category`, 
                    `id_product`,
                    `position`
                    ) VALUES (
                        '" . pSQL($value['id_category']) . "', 
                        '" . pSQL($value['id_product']) . "',
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

    private function populateCategoryShop($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "category_shop WHERE 1");
            $query->execute();

            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO ". _DB_PREFIX_ ."category_shop (
                    `id_category`, 
                    `id_shop`,
                    `position`
                    ) VALUES (
                        '" . pSQL($value['id_category']) . "', 
                        '" . pSQL($value['id_shop']) . "',
                        '" . pSQL($value['position']) . "'
                        )
                        ON DUPLICATE KEY UPDATE 
                        id_category = VALUES(id_category), 
                        id_shop = VALUES(id_shop), 
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

    private function populateCMSCategory($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "cms_category WHERE 1");
            $query->execute();

            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO ". _DB_PREFIX_ ."cms_category (
                    `id_cms_category`, 
                    `id_parent`,
                    `level_depth`,
                    `active`,
                    `date_add`,
                    `date_upd`,
                    `position`
                    ) VALUES (
                        '" . pSQL($value['id_cms_category']) . "', 
                        '" . pSQL($value['id_parent']) . "',
                        '" . pSQL($value['level_depth']) . "',
                        '" . pSQL($value['active']) . "',
                        '" . pSQL($value['date_add']) . "',
                        '" . pSQL($value['date_upd']) . "',
                        '" . pSQL($value['position']) . "'
                        )
                        ON DUPLICATE KEY UPDATE 
                    id_cms_category = VALUES(id_cms_category), 
                    id_parent = VALUES(id_parent), 
                    level_depth = VALUES(level_depth), 
                    active = VALUES(active), 
                    date_add = VALUES(date_add), 
                    date_upd = VALUES(date_upd), 
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

    private function populateCMSCategoryLang($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "cms_category_lang WHERE 1");
            $query->execute();

            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO ". _DB_PREFIX_ ."cms_category_lang (
                    `id_cms_category`, 
                    `id_lang`,
                    `id_shop`,
                    `name`,
                    `description`,
                    `link_rewrite`,
                    `meta_title`,
                    `meta_keywords`,
                    `meta_description`
                    ) VALUES (
                        '" . pSQL($value['id_cms_category']) . "', 
                        '" . pSQL($value['id_lang']) . "',
                        '" . pSQL($value['id_shop']) . "',
                        '" . pSQL($value['name']) . "',
                        '" . pSQL($value['description']) . "',
                        '" . pSQL($value['link_rewrite']) . "',
                        '" . pSQL($value['meta_title']) . "',
                        '" . pSQL($value['meta_keywords']) . "',
                        '" . pSQL($value['meta_description']) . "'
                        )
                        ON DUPLICATE KEY UPDATE 
                    id_cms_category = VALUES(id_cms_category), 
                    id_lang = VALUES(id_lang), 
                    id_shop = VALUES(id_shop), 
                    name = VALUES(name), 
                    description = VALUES(description), 
                    link_rewrite = VALUES(link_rewrite), 
                    meta_title = VALUES(meta_title), 
                    meta_keywords = VALUES(meta_keywords), 
                    meta_description = VALUES(meta_description);";
                Db::getInstance()->execute($sql);
            }
        }
        catch(PDOException $exception) {
            echo "Error: " . $exception->getMessage();
        }
        // Cerrar conexion
        $conn = null;
    }

    private function populateCMSCategoryShop($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "cms_category_shop WHERE 1");
            $query->execute();

            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO ". _DB_PREFIX_ ."cms_category_shop (
                    `id_cms_category`, 
                    `id_shop`
                    ) VALUES (
                        '" . pSQL($value['id_cms_category']) . "', 
                        '" . pSQL($value['id_shop']) . "'
                        )
                        ON DUPLICATE KEY UPDATE 
                        id_cms_category = VALUES(id_cms_category), 
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

    private function popullateLayeredCategory($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "layered_category WHERE 1");
            $query->execute();

            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO ". _DB_PREFIX_ ."layered_category (
                    `id_layered_category`, 
                    `id_shop`,
                    `id_category`,
                    `id_value`,
                    `type`,
                    `position`,
                    `filter_type`,
                    `filter_show_limit`,
                    `controller`
                    ) VALUES (
                        '" . pSQL($value['id_layered_category']) . "', 
                        '" . pSQL($value['id_shop']) . "',
                        '" . pSQL($value['id_category']) . "',
                        '" . pSQL($value['id_value']) . "',
                        '" . pSQL($value['type']) . "',
                        '" . pSQL($value['position']) . "',
                        '" . pSQL($value['filter_type']) . "',
                        '" . pSQL($value['filter_show_limit']) . "', 
                        'category'
                    )
                    ON DUPLICATE KEY UPDATE 
                    id_layered_category = VALUES(id_layered_category), 
                    id_shop = VALUES(id_shop), 
                    id_value = VALUES(id_value), 
                    type = VALUES(type), 
                    position = VALUES(position), 
                    filter_type = VALUES(filter_type), 
                    filter_show_limit = VALUES(filter_show_limit);";
                Db::getInstance()->execute($sql);
            }
        }
        catch(PDOException $exception) {
            echo "Error: " . $exception->getMessage();
        }
        // Cerrar conexion
        $conn = null;
    }

    public function populateAllCategories($conn, $prefix) {
        $this->populateCategory($conn,$prefix);
        $this->populateCategoryGroup($conn,$prefix);
        $this->populateCategoryLang($conn,$prefix);
        $this->populateCategoryShop($conn,$prefix);
        $this->populateCMSCategory($conn,$prefix);
        $this->populateCMSCategoryLang($conn,$prefix);
        $this->populateCMSCategoryShop($conn,$prefix);
        $this->popullateLayeredCategory($conn,$prefix);
        $this->populateCategoryProduct($conn,$prefix);
    }
}