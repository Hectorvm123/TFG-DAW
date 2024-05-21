<?php
class CartPopulator{
    private function populateCart($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "cart WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."cart WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "cart (
                    `id_cart`, 
                    `id_shop_group`,
                    `id_shop`,
                    `id_carrier`,
                    `delivery_option`,
                    `id_lang`,
                    `id_addresses_delivery`,
                    `id_addresses_invoice`,
                    `id_currency`,
                    `id_customer`,
                    `id_guest`,
                    `secure_key`,
                    `recyclable`,
                    `gift`,
                    `gift_message`,
                    `mobile_theme`,
                    `allow_separated_packages`,
                    `date_add`,
                    `date_upd`
                ) 
                VALUES (
                    '" . pSQL($value['id_cart']) . "', 
                    '" . pSQL($value['id_shop_group']) . "',
                    '" . pSQL($value['id_shop']) . "',
                    '" . pSQL($value['id_carrier']) . "',
                    '" . pSQL($value['delivery_option']) . "',
                    '" . pSQL($value['id_lang']) . "',
                    '" . pSQL($value['id_addresses_delivery']) . "',
                    '" . pSQL($value['id_addresses_invoice']) . "',
                    '" . pSQL($value['id_currency']) . "',
                    '" . pSQL($value['id_customer']) . "',
                    '" . pSQL($value['id_guest']) . "',
                    '" . pSQL($value['secure_key']) . "',
                    '" . pSQL($value['recyclable']) . "',
                    '" . pSQL($value['gift']) . "',
                    '" . pSQL($value['gift_message']) . "',
                    '" . pSQL($value['mobile_theme']) . "',
                    '" . pSQL($value['allow_separated_packages']) . "',
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


    private function populateCartCartRule($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "cart_cart_rule WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."cart_cart_rule WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO ". _DB_PREFIX_ ."cart_cart_rule (
                    `id_cart`, 
                    `id_cart_rule`
                    ) VALUES (
                        '" . pSQL($value['id_cart']) . "', 
                        '" . pSQL($value['id_cart_rule']) . "'
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


    private function populateCartProduct($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "cart_product WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."cart_product WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "cart_product (
                    `id_cart`, 
                    `id_product`,
                    `id_address_delivery`,
                    `id_shop`,
                    `id_product_attribute`,
                    `quantity`,
                    `date_add`,
                    `id_customization`
                ) 
                VALUES (
                    '" . pSQL($value['id_cart']) . "', 
                    '" . pSQL($value['id_product']) . "',
                    '" . pSQL($value['id_address_delivery']) . "',
                    '" . pSQL($value['id_shop']) . "',
                    '" . pSQL($value['id_product_attribute']) . "',
                    '" . pSQL($value['quantity']) . "',
                    '" . pSQL($value['date_add']) . "',
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

    private function populateCartRule($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "cart_rule WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."cart_rule WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "cart_rule (
                    `id_cart_rule`,
                    `id_customer`, 
                    `date_from`,
                    `date_to`,
                    `description`,
                    `quantity_per_user`,
                    `priority`,
                    `partial_use`,
                    `code`,
                    `minimum_amount`,
                    `minimum_amount_tax`,
                    `minimum_amount_currency`,
                    `minimum_amount_shipping`,
                    `country_restriction`, 
                    `carrier_restriction`,
                    `cart_rule_restriction`,
                    `shop_restriction`,
                    `free_shipping`,
                    `reduction_percent`,
                    `reduction_amount`,
                    `reduction_tax`,
                    `reduction_currency`,
                    `reduction_product`,
                    `gift_product`,
                    `highlight`,
                    `active`,
                    `date_add`,
                    `date_upd`,
                ) 
                VALUES (
                    '" . pSQL($value['id_cart_rule']) . "',
                    '" . pSQL($value['id_customer']) . "', 
                    '" . pSQL($value['date_from']) . "',
                    '" . pSQL($value['date_to']) . "',
                    '" . pSQL($value['description']) . "',
                    '" . pSQL($value['s_per_user']) . "',
                    '" . pSQL($value['priority']) . "',
                    '" . pSQL($value['partial_use']) . "',
                    '" . pSQL($value['code']) . "',
                    '" . pSQL($value['minimum_amount']) . "',
                    '" . pSQL($value['minimum_amount_tax']) . "',
                    '" . pSQL($value['minimum_amount_currency']) . "',
                    '" . pSQL($value['minimum_amount_shipping']) . "',
                    '" . pSQL($value['country_restriction']) . "', 
                    '" . pSQL($value['carrier_restriction']) . "',
                    '" . pSQL($value['cart_rule_restriction']) . "',
                    '" . pSQL($value['shop_restriction']) . "',
                    '" . pSQL($value['free_shipping']) . "',
                    '" . pSQL($value['reduction_percent']) . "',
                    '" . pSQL($value['reduction_amount']) . "',
                    '" . pSQL($value['reduction_tax']) . "',
                    '" . pSQL($value['reduction_currency']) . "',
                    '" . pSQL($value['reduction_product']) . "',
                    '" . pSQL($value['gift_product']) . "',
                    '" . pSQL($value['highlight']) . "',
                    '" . pSQL($value['active']) . "',
                    '" . pSQL($value['date_add']) . "',
                    '" . pSQL($value['date_upd']) . "',

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

    private function populateCartRuleCarrier($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "cart_rule_carrier WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."cart_rule_carrier WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO ". _DB_PREFIX_ ."cart_rule_carrier (
                    `id_cart_rule`, 
                    `id_carrier`
                    
                    ) VALUES (
                        '" . pSQL($value['id_cart_rule']) . "', 
                        '" . pSQL($value['id_carrier']) . "'
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

    private function populateCartRuleCombination($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "cart_rule_combination WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."cart_rule_combination WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO ". _DB_PREFIX_ ."cart_rule_combination (
                    `id_cart_rule_1`, 
                    `id_cart_rule_2`
                    ) VALUES (
                        '" . pSQL($value['id_cart_rule_1']) . "', 
                        '" . pSQL($value['id_cart_rule_2']) . "'
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

    private function populateCartRuleCountry($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "cart_rule_country WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."cart_rule_country WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO ". _DB_PREFIX_ ."cart_rule_country (
                    `id_cart_rule`, 
                    `id_country`
                    ) VALUES (
                        '" . pSQL($value['id_cart_rule']) . "', 
                        '" . pSQL($value['id_country']) . "'
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
    
    

    private function populateCartRuleGroup($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "cart_rule_group WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."cart_rule_group WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO ". _DB_PREFIX_ ."cart_rule_group (
                    `id_cart_group`, 
                    `id_group`
                    ) VALUES (
                        '" . pSQL($value['id_cart_group']) . "', 
                        '" . pSQL($value['id_group']) . "'
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