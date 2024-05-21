<?php
class ProductPopulator{
    public function populateProduct($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "product WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."product WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "product (
                    `id_product`, 
                    `id_supplier`,
                    `id_manufacturer`,
                    `id_category_default`,
                    `id_shop_default`,
                    `id_tax_rules_group`,
                    `on_sale`,
                    `online_only`,
                    `ean13`,
                    `isbn`,
                    `upc`,
                    `mpn`,
                    `ecotax`,
                    `quantity`,
                    `minimal_quantity`,
                    `low_stock_threshold`,
                    `low_stock_alert`,
                    `price`,
                    `wholesale_price`,
                    `unity`,
                    `unit_price`,
                    `unit_price_ratio`,
                    `additional_shipping_cost`,
                    `reference`,
                    `supplier_reference`,
                    `location`,
                    `width`,
                    `height`,
                    `depth`,
                    `weight`,
                    `out_of_stock`,
                    `additional_delivery_times`,
                    `quantity_discount`,
                    `customizable`,
                    `uploadable_files`,
                    `text_fields`,
                    `active`,
                    `redirect_type`,
                    `id_type_redirected`,
                    `available_for_order`,
                    `available_date`,
                    `show_condition`,
                    `condition`,
                    `show_price`,
                    `indexed`,
                    `visibility`,
                    `cache_is_pack`,
                    `cache_has_attachments`,
                    `is_virtual`,
                    `cache_default_attribute`,
                    `date_add`,
                    `date_upd`,
                    `advanced_stock_management`,
                    `pack_stock_type`,
                    `state`,
                    `product_type`
                ) 
                VALUES (
                    '" . pSQL($value['id_product']) . "', 
                    '" . pSQL($value['id_supplier']) . "',
                    '" . pSQL($value['id_manufacturer']) . "',
                    '" . pSQL($value['id_category_default']) . "',
                    '" . pSQL($value['id_shop_default']) . "',
                    '" . pSQL($value['id_tax_rules_group']) . "',
                    '" . pSQL($value['on_sale']) . "',
                    '" . pSQL($value['online_only']) . "',
                    '" . pSQL($value['ean13']) . "',
                    '',
                    '" . pSQL($value['upc']) . "',
                    '',
                    '" . pSQL($value['ecotax']) . "',
                    '" . pSQL($value['quantity']) . "',
                    '" . pSQL($value['minimal_quantity']) . "',
                    0,
                    0,
                    '" . pSQL($value['price']) . "',
                    '" . pSQL($value['wholesale_price']) . "',
                    '" . pSQL($value['unity']) . "',
                    0,
                    '" . pSQL($value['unit_price_ratio']) . "',
                    '" . pSQL($value['additional_shipping_cost']) . "',
                    '" . pSQL($value['reference']) . "',
                    '" . pSQL($value['supplier_reference']) . "',
                    '" . pSQL($value['location']) . "',
                    '" . pSQL($value['width']) . "',
                    '" . pSQL($value['height']) . "',
                    '" . pSQL($value['depth']) . "',
                    '" . pSQL($value['weight']) . "',
                    '" . pSQL($value['out_of_stock']) . "',
                    0,
                    '" . pSQL($value['quantity_discount']) . "',
                    '" . pSQL($value['customizable']) . "',
                    '" . pSQL($value['uploadable_files']) . "',
                    '" . pSQL($value['text_fields']) . "',
                    '" . pSQL($value['active']) . "',
                    '" . pSQL($value['redirect_type']) . "',
                    '" . pSQL($value['id_product_redirected']) . "',
                    '" . pSQL($value['available_for_order']) . "',
                    '" . pSQL($value['available_date']) . "',
                    1,
                    '" . pSQL($value['condition']) . "',
                    '" . pSQL($value['show_price']) . "',
                    '" . pSQL($value['indexed']) . "',
                    '" . pSQL($value['visibility']) . "',
                    '" . pSQL($value['cache_is_pack']) . "',
                    '" . pSQL($value['cache_has_attachments']) . "',
                    '" . pSQL($value['is_virtual']) . "',
                    '" . pSQL($value['cache_default_attribute']) . "',
                    '" . pSQL($value['date_add']) . "',
                    '" . pSQL($value['date_upd']) . "',
                    '" . pSQL($value['advanced_stock_management']) . "',
                    0,
                    1,
                    0
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


    private function populateProductLang($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "product_lang WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."product_lang WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO ". _DB_PREFIX_ ."product_lang (
                    `id_product`, 
                    `id_shop`,
                    `id_lang`,
                    `description`,
                    `description_short`,
                    `link_rewrite`,
                    `meta_description`,
                    `meta_keywords`,
                    `meta_title`,
                    `name`,
                    `available_now`,
                    `available_later`
                    ) VALUES (
                        '" . pSQL($value['id_product']) . "', 
                        '" . pSQL($value['id_shop']) . "',
                        '" . pSQL($value['id_lang']) . "',
                        '" . pSQL($value['description']) . "',
                        '<p>" . pSQL($value['description_short']) . "</p>',
                        '" . pSQL($value['link_rewrite']) . "',
                        '" . pSQL($value['meta_description']) . "',
                        '" . pSQL($value['meta_keywords']) . "',
                        '" . pSQL($value['meta_title']) . "',
                        '" . pSQL($value['name']) . "',
                        '" . pSQL($value['available_now']) . "',
                        '" . pSQL($value['available_later']) . "'
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


    private function populateProductShop($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "product_shop WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."product_shop WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "product_shop (
                    `id_product`, 
                    `id_shop`,
                    `id_category_default`,
                    `id_tax_rules_group`,
                    `on_sale`,
                    `online_only`,
                    `ecotax`,
                    `minimal_quantity`,
                    `price`,
                    `wholesale_price`,
                    `unity`,
                    `unit_price_ratio`,
                    `additional_shipping_cost`,
                    `customizable`,
                    `uploadable_files`,
                    `text_fields`,
                    `active`,
                    `redirect_type`,
                    `id_type_redirected`,
                    `available_for_order`,
                    `available_date`,
                    `condition`,
                    `show_price`,
                    `indexed`,
                    `visibility`,
                    `cache_default_attribute`,
                    `advanced_stock_management`,
                    `date_add`,
                    `date_upd`
                ) 
                VALUES (
                    '" . pSQL($value['id_product']) . "', 
                    '" . pSQL($value['id_shop']) . "',
                    '" . pSQL($value['id_category_default']) . "',
                    '" . pSQL($value['id_tax_rules_group']) . "',
                    '" . pSQL($value['on_sale']) . "',
                    '" . pSQL($value['online_only']) . "',
                    '" . pSQL($value['ecotax']) . "',
                    '" . pSQL($value['minimal_quantity']) . "',
                    '" . pSQL($value['price']) . "',
                    '" . pSQL($value['wholesale_price']) . "',
                    '" . pSQL($value['unity']) . "',
                    '" . pSQL($value['unit_price_ratio']) . "',
                    '" . pSQL($value['additional_shipping_cost']) . "',
                    '" . pSQL($value['customizable']) . "',
                    '" . pSQL($value['uploadable_files']) . "',
                    '" . pSQL($value['text_fields']) . "',
                    '" . pSQL($value['active']) . "',
                    '" . pSQL($value['redirect_type']) . "',
                    '" . pSQL($value['id_product_redirected']) . "',
                    '" . pSQL($value['available_for_order']) . "',
                    '" . pSQL($value['available_date']) . "',
                    '" . pSQL($value['condition']) . "',
                    '" . pSQL($value['show_price']) . "',
                    '" . pSQL($value['indexed']) . "',
                    '" . pSQL($value['visibility']) . "',
                    '" . pSQL($value['cache_default_attribute']) . "',
                    '" . pSQL($value['advanced_stock_management']) . "',
                    '" . pSQL($value['date_add']) . "',
                    '" . pSQL($value['date_upd']) . "'
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

    private function populateFeatureProduct($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "feature_product WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."feature_product WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO ". _DB_PREFIX_ ."feature_product (
                    `id_product`, 
                    `id_feature`,
                    `id_feature_value`
                    
                    ) VALUES (
                        '" . pSQL($value['id_product']) . "', 
                        '" . pSQL($value['id_feature']) . "',
                        '" . pSQL($value['id_feature_value']) . "'
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

    private function populateLayeredProductAttribute($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "layered_product_attribute WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."layered_product_attribute WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO ". _DB_PREFIX_ ."layered_product_attribute (
                    `id_product`, 
                    `id_attribute`,
                    `id_attribute_group`,
                    `id_shop`
                    ) VALUES (
                        '" . pSQL($value['id_product']) . "', 
                        '" . pSQL($value['id_attribute']) . "',
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

    private function populateProductAttachment($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "product_attachment WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."product_attachment WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO ". _DB_PREFIX_ ."product_attachment (
                    `id_product`, 
                    `id_attachment`
                    ) VALUES (
                        '" . pSQL($value['id_product']) . "', 
                        '" . pSQL($value['id_attachment']) . "'
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
    
    private function populateProductAttribute($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "product_attribute WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."product_attribute WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "product_attribute (
                    `id_product_attribute`,
                    `id_product`, 
                    `reference`,
                    `supplier_reference`,
                    `ean13`,
                    `isbn`,
                    `upc`,
                    `mpn`,
                    `wholesale_price`,
                    `price`,
                    `ecotax`,
                    `low_stock_threshold`,
                    `low_stock_alert`,
                    `weight`,
                    `unit_price_impact`,
                    `default_on`,
                    `minimal_quantity`,
                    `available_date`
                ) 
                VALUES (
                    '" . pSQL($value['id_product_attribute']) . "',
                    '" . pSQL($value['id_product']) . "', 
                    '" . pSQL($value['reference']) . "',
                    '" . pSQL($value['supplier_reference']) . "',
                    '" . pSQL($value['ean13']) . "',
                    '',
                    '" . pSQL($value['upc']) . "',
                    '',
                    '" . pSQL($value['wholesale_price']) . "',
                    '" . pSQL($value['price']) . "',
                    '" . pSQL($value['ecotax']) . "',
                    0,
                    0,
                    '" . pSQL($value['weight']) . "',
                    '" . pSQL($value['unit_price_impact']) . "',
                    NULL,
                    '" . pSQL($value['minimal_quantity']) . "',
                    '" . pSQL($value['available_date']) . "'

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

    private function populateProductAttributeCombination($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "product_attribute_combination WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."product_attribute_combination WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO ". _DB_PREFIX_ ."product_attribute_combination (
                    `id_attribute`, 
                    `id_product_attribute`
                    ) VALUES (
                        '" . pSQL($value['id_attribute']) . "', 
                        '" . pSQL($value['id_product_attribute']) . "'
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
    private function populateProductAttributeImage($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "product_attribute_image WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."product_attribute_image WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO ". _DB_PREFIX_ ."product_attribute_image (
                    `id_image`, 
                    `id_product_attribute`
                    ) VALUES (
                        '" . pSQL($value['id_image']) . "', 
                        '" . pSQL($value['id_product_attribute']) . "'
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

    private function populateProductAttributeShop($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "product_attribute_shop WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."product_attribute_shop WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "product_attribute_shop (
                    `id_product`, 
                    `id_product_attribute`, 
                    `id_shop`,
                    `wholesale_price`,
                    `price`,
                    `ecotax`,
                    `weight`,
                    `unit_price_impact`,
                    `default_on`,
                    `minimal_quantity`,
                    `available_date`
                ) 
                VALUES (
                    '" . pSQL($value['id_product']) . "', 
                    '" . pSQL($value['id_product_attribute']) . "', 
                    '" . pSQL($value['id_shop']) . "',
                    '" . pSQL($value['wholesale_price']) . "',
                    '" . pSQL($value['price']) . "',
                    '" . pSQL($value['ecotax']) . "',
                    '" . pSQL($value['weight']) . "',
                    '" . pSQL($value['unit_price_impact']) . "',
                    NULL,
                    '" . pSQL($value['minimal_quantity']) . "',
                    '" . pSQL($value['available_date']) . "'
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

    private function populateProductCarrier($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "product_carrier WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."product_carrier WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO ". _DB_PREFIX_ ."product_carrier (
                    `id_product`, 
                    `id_carrier_reference`
                    `id_shop`
                    ) VALUES (
                        '" . pSQL($value['id_product']) . "', 
                        '" . pSQL($value['id_carrier_reference']) . "'
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
    
    private function populateProductCountryTax($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "product_country_tax WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."product_country_tax WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "product_country_tax (
                    `id_product`, 
                    `id_country`, 
                    `id_tax`
                ) 
                VALUES (
                    '" . pSQL($value['id_product']) . "', 
                    '" . pSQL($value['id_country']) . "', 
                    '" . pSQL($value['id_tax']) . "'
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

    private function populateProductDownload($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "product_download WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."product_download WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "product_download (
                    `id_product`, 
                    `id_product_download`, 
                    `display_name`,
                    `date_add`,
                    `date_expiration`,
                    `nb_days_accessible`,
                    `nb_downloadable`,
                    `active`,
                    `is_shareable`
                ) 
                VALUES (
                    '" . pSQL($value['id_product']) . "', 
                    '" . pSQL($value['id_product_download']) . "', 
                    '" . pSQL($value['display_name']) . "',
                    '" . pSQL($value['date_add']) . "',
                    '" . pSQL($value['date_expiration']) . "',
                    '" . pSQL($value['nb_days_accessible']) . "',
                    '" . pSQL($value['nb_downloadable']) . "',
                    '" . pSQL($value['active']) . "',
                    '" . pSQL($value['is_shareable']) . "'
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

    private function populateProductSale($conn, $prefix){
            try {
                $query = $conn->prepare("SELECT * FROM " .$prefix. "product_sale WHERE 1");
                $query->execute();

                Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."product_sale WHERE 1;");
                foreach($query->fetchAll() as $key=>$value) {
                    $sql = "INSERT INTO " . _DB_PREFIX_ . "product_sale (
                        `id_product`, 
                        `quantity`,
                        `sale_nbr`,
                        `date_upd`
                    ) 
                    VALUES (
                        '" . pSQL($value['id_product']) . "', 
                        '" . pSQL($value['quantity']) . "',
                        '" . pSQL($value['sale_nbr']) . "',
                        '" . pSQL($value['date_upd']) . "'
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

    private function populateProductSupplier($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "product_supplier WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."product_supplier WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "product_supplier (
                    `id_product`, 
                    `id_product_supplier`, 
                    `id_product_attribute`,
                    `id_supplier`,
                    `product_supplier_reference`,
                    `product_supplier_price_te`,
                    `id_currency`
                ) 
                VALUES (
                    '" . pSQL($value['id_product']) . "', 
                    '" . pSQL($value['id_product_supplier']) . "', 
                    '" . pSQL($value['id_product_attribute']) . "',
                    '" . pSQL($value['id_supplier']) . "',
                    '" . pSQL($value['product_supplier_reference']) . "',
                    '" . pSQL($value['product_supplier_price_te']) . "',
                    '" . pSQL($value['id_currency']) . "'
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

    private function populateProductTag($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "product_tag WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."product_tag WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "product_tag (
                    `id_product`, 
                    `id_tag`,
                    `id_lang`
                ) 
                VALUES (
                    '" . pSQL($value['id_product']) . "', 
                    '" . pSQL($value['id_tag']) . "',
                    1
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

    private function populateWareHouseProductLocation($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "warehouse_product_location WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."warehouse_product_location WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "warehouse_product_location (
                    `id_product`, 
                    `id_warehouse_product_location`, 
                    `id_product_attribute`,
                    `id_warehouse`,
                    `location`
                ) 
                VALUES (
                    '" . pSQL($value['id_product']) . "', 
                    '" . pSQL($value['id_warehouse_product_location']) . "', 
                    '" . pSQL($value['id_product_attribute']) . "',
                    '" . pSQL($value['id_warehouse']) . "',
                    '" . pSQL($value['location']) . "'
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


    private function populateProductComment($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "product_comment WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."product_comment WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "product_comment (
                    `id_product`, 
                    `id_product_comment`, 
                    `id_customer`,
                    `id_guest`,
                    `title`,
                    `content`,
                    `customer_name`,
                    `grade`,
                    `validate`,
                    `deleted`,
                    `date_add`
                ) 
                VALUES (
                    '" . pSQL($value['id_product']) . "', 
                    '" . pSQL($value['id_product_comment']) . "', 
                    '" . pSQL($value['id_customer']) . "',
                    '" . pSQL($value['id_guest']) . "',
                    '" . pSQL($value['title']) . "',
                    '" . pSQL($value['content']) . "',
                    '" . pSQL($value['customer_name']) . "',
                    '" . pSQL($value['grade']) . "',
                    '" . pSQL($value['validate']) . "',
                    '" . pSQL($value['deleted']) . "',
                    '" . pSQL($value['date_add']) . "'
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

    private function populateProductCommentCriterion($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "product_comment_criterion WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."product_comment_criterion WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "product_comment_criterion (
                    `id_product_comment_criterion`, 
                    `id_product_comment_criterion_type`, 
                    `active`
                ) 
                VALUES (
                    '" . pSQL($value['id_product_comment_criterion']) . "', 
                    '" . pSQL($value['id_product_comment_criterion_type']) . "', 
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

    private function populateProductCommentCriterionCategory($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "product_comment_criterion_category WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."product_comment_criterion_category WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "product_comment_criterion_category (
                    `id_product_comment_criterion`, 
                    `id_category`
                ) 
                VALUES (
                    '" . pSQL($value['id_product_comment_criterion']) . "', 
                    '" . pSQL($value['id_category']) . "'
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


    private function populateProductCommentCriterionLang($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "product_comment_criterion_lang WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."product_comment_criterion_lang WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "product_comment_criterion_lang (
                    `id_product_comment_criterion`, 
                    `id_lang`, 
                    `name`
                ) 
                VALUES (
                    '" . pSQL($value['id_product_comment_criterion']) . "', 
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

    private function populateProductCommentCriterionProduct($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "product_comment_criterion_product WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."product_comment_criterion_product WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "product_comment_criterion_product (
                    `id_product_comment_criterion`, 
                    `id_product`
                ) 
                VALUES (
                    '" . pSQL($value['id_product_comment_criterion']) . "', 
                    '" . pSQL($value['id_product']) . "'
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

    private function populateProductCommentCriterionGrade($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "product_comment_criterion_grade WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."product_comment_criterion_grade WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "product_comment_criterion_grade (
                    `id_product_comment_criterion`, 
                    `id_product_comment`, 
                    `grade`
                ) 
                VALUES (
                    '" . pSQL($value['id_product_comment_criterion']) . "', 
                    '" . pSQL($value['id_product_comment']) . "', 
                    '" . pSQL($value['grade']) . "'
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

    private function populateProductCommentRepeat($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "product_comment_repeat WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."product_comment_repeat WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "product_comment_repeat (
                    `id_product_comment`, 
                    `id_customer`
                ) 
                VALUES (
                    '" . pSQL($value['id_product_comment']) . "', 
                    '" . pSQL($value['id_customer']) . "'
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

    private function populateProductCommentUsefulness($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "product_comment_usefulness WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."product_comment_usefulness WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "product_comment_usefulness (
                    `id_product_comment`, 
                    `id_customer`, 
                    `usefulness`
                ) 
                VALUES (
                    '" . pSQL($value['id_product_comment']) . "', 
                    '" . pSQL($value['id_customer']) . "', 
                    '" . pSQL($value['usefulness']) . "'
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
    


    public function populateAllProducts($conn, $prefix) {
        $this->populateProduct($conn,$prefix);
        $this->populateProductLang($conn,$prefix);
        $this->populateProductShop($conn,$prefix);
        $this->populateFeatureProduct($conn,$prefix);
        $this->populateLayeredProductAttribute($conn,$prefix);
        $this->populateProductAttachment($conn,$prefix);
        $this->populateProductAttribute($conn,$prefix);
        $this->populateProductAttributeCombination($conn,$prefix);
        $this->populateProductAttributeImage($conn,$prefix);
        $this->populateProductAttributeShop($conn,$prefix);
        $this->populateProductCarrier($conn,$prefix);
        $this->populateProductCountryTax($conn,$prefix);
        $this->populateProductDownload($conn,$prefix);
        $this->populateProductSale($conn,$prefix);
        $this->populateProductSupplier($conn,$prefix);
        $this->populateProductTag($conn,$prefix);
        $this->populateWareHouseProductLocation($conn,$prefix);

        //  PRODUCT COMMENTS (these tables ain't on the 1.6)-------------------------

        // $this->populateProductComment($conn,$prefix);
        // $this->populateProductCommentCriterion($conn,$prefix);
        //$this->populateProductCommentCriterionCategory($conn,$prefix);
        //$this->populateProductCommentCriterionLang($conn,$prefix);
        //$this->populateProductCommentCriterionProduct($conn,$prefix);
        //$this->populateProductCommentCriterionGrade($conn,$prefix);
        //$this->populateProductCommentRepeat($conn,$prefix);
        //$this->populateProductCommentUsefulness($conn,$prefix);
    }
}