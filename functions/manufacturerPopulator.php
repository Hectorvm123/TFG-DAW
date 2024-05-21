<?php
class ManufacturerPopulator{
    public function populateManufacturer($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "manufacturer WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."manufacturer WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "manufacturer (
                    `id_manufacturer`,
                    `name`, 
                    `date_add`,
                    `date_upd`,
                    `active`
                ) 
                VALUES (
                    '" . pSQL($value['id_manufacturer']) . "',
                    '" . pSQL($value['name']) . "', 
                    '" . pSQL($value['date_add']) . "',
                    '" . pSQL($value['date_upd']) . "',
                    '" . pSQL($value['active']) . "'

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

    public function populateManufacturerLang($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "manufacturer_lang WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."manufacturer_lang WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "manufacturer_lang (
                    `id_manufacturer`,
                    `id_lang`, 
                    `description`,
                    `short_description`,
                    `meta_title`,
                    `meta_keywords`,
                    `meta_description`
                ) 
                VALUES (
                    '" . pSQL($value['id_manufacturer']) . "',
                    '" . pSQL($value['id_lang']) . "', 
                    '" . pSQL($value['description']) . "',
                    '<p>" . pSQL($value['short_description']) . "</p>',
                    '" . pSQL($value['meta_title']) . "',
                    '" . pSQL($value['meta_keywords']) . "',
                    '" . pSQL($value['meta_description']) . "'

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

    public function populateManufacturerShop($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "manufacturer_shop WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."manufacturer_shop WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "manufacturer_shop (
                    `id_manufacturer`,
                    `id_shop`
                ) 
                VALUES (
                    '" . pSQL($value['id_manufacturer']) . "',
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
    
    


    public function populateAllManufacturers($conn, $prefix) {
        $this->populateManufacturer($conn,$prefix);
        $this->populateManufacturerLang($conn,$prefix);
        $this->populateManufacturerShop($conn,$prefix);
        

       
    }
}