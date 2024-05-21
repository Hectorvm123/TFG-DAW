<?php
class OrderPopulator{
    private function populateOrders($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "orders WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."orders WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "orders (
                    `id_order`, 
                    `reference`,
                    `id_shop_group`,
                    `id_shop`,
                    `id_carrier`,
                    `id_lang`,
                    `id_customer`,
                    `id_cart`,
                    `id_currency`,
                    `id_address_invoice`,
                    `id_address_delivery`,
                    `current_state`,
                    `secure_key`,
                    `payment`,
                    `conversion_rate`,
                    `module`,
                    `recyclable`,
                    `gift`,
                    `gift_message`,
                    `mobile_theme`,
                    `total_discounts`,
                    `total_discounts_tax_incl`,
                    `total_discounts_tax_excl`,
                    `total_paid`,
                    `total_paid_tax_incl`,
                    `total_paid_tax_excl`,
                    `total_paid_real`,
                    `total_products`,
                    `total_products_wt`,
                    `total_shipping`,
                    `total_shipping_tax_incl`,
                    `total_shipping_tax_excl`,
                    `carrier_tax_rate`,
                    `total_wrapping`,
                    `total_wrapping_tax_incl`,
                    `total_wrapping_tax_excl`,
                    `invoice_number`,
                    `delivery_number`,
                    `invoice_date`,
                    `delivery_date`,
                    `valid`,
                    `date_add`,
                    `date_upd`,
                    `round_mode`,
                    `round_type`
                ) 
                VALUES (
                    '" . pSQL($value['id_order']) . "', 
                    '" . pSQL($value['reference']) . "',
                    '" . pSQL($value['id_shop_group']) . "',
                    '" . pSQL($value['id_shop']) . "',
                    '" . pSQL($value['id_carrier']) . "',
                    '" . pSQL($value['id_lang']) . "',
                    '" . pSQL($value['id_customer']) . "',
                    '" . pSQL($value['id_cart']) . "',
                    '" . pSQL($value['id_currency']) . "',
                    '" . pSQL($value['id_address_invoice']) . "',
                    '" . pSQL($value['id_address_delivery']) . "',
                    '" . pSQL($value['current_state']) . "',
                    '" . pSQL($value['secure_key']) . "',
                    '" . pSQL($value['payment']) . "',
                    '" . pSQL($value['conversion_rate']) . "',
                    '" . pSQL($value['module']) . "',
                    '" . pSQL($value['recyclable']) . "',
                    '" . pSQL($value['gift']) . "',
                    '" . pSQL($value['gift_message']) . "',
                    '" . pSQL($value['mobile_theme']) . "',
                    '" . pSQL($value['total_discounts']) . "',
                    '" . pSQL($value['total_discounts_tax_incl']) . "',
                    '" . pSQL($value['total_discounts_tax_excl']) . "',
                    '" . pSQL($value['total_paid']) . "',
                    '" . pSQL($value['total_paid_tax_incl']) . "',
                    '" . pSQL($value['total_paid_tax_excl']) . "',
                    '" . pSQL($value['total_paid_real']) . "',
                    '" . pSQL($value['total_products']) . "',
                    '" . pSQL($value['total_products_wt']) . "',
                    '" . pSQL($value['total_shipping']) . "',
                    '" . pSQL($value['total_shipping_tax_incl']) . "',
                    '" . pSQL($value['total_shipping_tax_excl']) . "',
                    '" . pSQL($value['carrier_tax_rate']) . "',
                    '" . pSQL($value['total_wrapping']) . "',
                    '" . pSQL($value['total_wrapping_tax_incl']) . "',
                    '" . pSQL($value['total_wrapping_tax_excl']) . "',
                    '" . pSQL($value['invoice_number']) . "',
                    '" . pSQL($value['delivery_number']) . "',
                    '" . pSQL($value['invoice_date']) . "',
                    '" . pSQL($value['delivery_date']) . "',
                    '" . pSQL($value['valid']) . "',
                    '" . pSQL($value['date_add']) . "',
                    '" . pSQL($value['date_upd']) . "',
                    0,
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

    private function populateOrderDetail($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "order_detail WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."order_detail WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "order_detail (
                    `id_order`, 
                    `id_order_detail`,
                    `id_order_invoice`,
                    `id_warehouse`,
                    `id_shop`,
                    `product_id`,
                    `product_attribute_id`,
                    `product_name`,
                    `product_quantity`,
                    `product_quantity_in_stock`,
                    `product_quantity_refunded`,
                    `product_quantity_return`,
                    `product_price`,
                    `product_quantity_reinjected`,
                    `reduction_percent`,
                    `reduction_amount`,
                    `reduction_amount_tax_incl`,
                    `reduction_amount_tax_excl`,
                    `group_reduction`,
                    `product_quantity_discount`,
                    `product_ean13`,
                    `product_upc`,
                    `product_isbn`,
                    `product_mpn`,
                    `product_reference`,
                    `product_weight`,
                    `tax_computation_method`,
                    `tax_name`,
                    `tax_rate`,
                    `ecotax`,
                    `ecotax_tax_rate`,
                    `discount_quantity_applied`,
                    `download_hash`,
                    `download_nb`,
                    `download_deadline`,
                    `total_price_tax_incl`,
                    `total_price_tax_excl`,
                    `unit_price_tax_incl`,
                    `unit_price_tax_excl`,
                    `total_shipping_price_tax_incl`,
                    `total_shipping_price_tax_excl`,
                    `purchase_supplier_price`,
                    `original_product_price`
                ) 
                VALUES (
                    '" . pSQL($value['id_order']) . "', 
                    '" . pSQL($value['id_order_detail']) . "',
                    '" . pSQL($value['id_order_invoice']) . "',
                    '" . pSQL($value['id_warehouse']) . "',
                    '" . pSQL($value['id_shop']) . "',
                    '" . pSQL($value['product_id']) . "',
                    '" . pSQL($value['product_attribute_id']) . "',
                    '" . pSQL($value['product_name']) . "',
                    '" . pSQL($value['product_quantity']) . "',
                    '" . pSQL($value['product_quantity_in_stock']) . "',
                    '" . pSQL($value['product_quantity_refunded']) . "',
                    '" . pSQL($value['product_quantity_return']) . "',
                    '" . pSQL($value['product_price']) . "',
                    '" . pSQL($value['product_quantity_reinjected']) . "',
                    '" . pSQL($value['reduction_percent']) . "',
                    '" . pSQL($value['reduction_amount']) . "',
                    '" . pSQL($value['reduction_amount_tax_incl']) . "',
                    '" . pSQL($value['reduction_amount_tax_excl']) . "',
                    '" . pSQL($value['group_reduction']) . "',
                    '" . pSQL($value['product_quantity_discount']) . "',
                    '" . pSQL($value['product_ean13']) . "',
                    '" . pSQL($value['product_upc']) . "',
                    0,
                    0,
                    '" . pSQL($value['product_reference']) . "',
                    '" . pSQL($value['product_weight']) . "',
                    '" . pSQL($value['tax_computation_method']) . "',
                    '" . pSQL($value['tax_name']) . "',
                    '" . pSQL($value['tax_rate']) . "',
                    '" . pSQL($value['ecotax']) . "',
                    '" . pSQL($value['ecotax_tax_rate']) . "',
                    '" . pSQL($value['discount_quantity_applied']) . "',
                    '" . pSQL($value['download_hash']) . "',
                    '" . pSQL($value['download_nb']) . "',
                    '" . pSQL($value['download_deadline']) . "',
                    '" . pSQL($value['total_price_tax_incl']) . "',
                    '" . pSQL($value['total_price_tax_excl']) . "',
                    '" . pSQL($value['unit_price_tax_incl']) . "',
                    '" . pSQL($value['unit_price_tax_excl']) . "',
                    '" . pSQL($value['total_shipping_price_tax_incl']) . "',
                    '" . pSQL($value['total_shipping_price_tax_excl']) . "',
                    '" . pSQL($value['purchase_supplier_price']) . "',
                    '" . pSQL($value['original_product_price']) . "'
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

    private function populateOrderHistory($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "order_history WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."order_history WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "order_history (
                    `id_order_history`, 
                    `id_employee`,
                    `id_order`,
                    `id_order_state`,
                    `date_add`
                ) 
                VALUES (
                    '" . pSQL($value['id_order_history']) . "', 
                    '" . pSQL($value['id_employee']) . "',
                    '" . pSQL($value['id_order']) . "',
                    '" . pSQL($value['id_order_state']) . "',
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

    private function populateOrderInvoice($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "order_invoice WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."order_invoice WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "order_invoice (
                    `id_order`, 
                    `id_order_invoice`,
                    `number`,
                    `delivery_number`,
                    `delivery_date`,
                    `total_discount_tax_incl`,
                    `total_discount_tax_excl`,
                    `total_paid_tax_incl`,
                    `total_paid_tax_excl`,
                    `total_products`,
                    `total_products_wt`,
                    `total_shipping_tax_incl`,
                    `total_shipping_tax_excl`,
                    `total_wrapping_tax_incl`,
                    `total_wrapping_tax_excl`,
                    `shipping_tax_computation_method`,
                    `note`,
                    `date_add`
                ) 
                VALUES (
                    '" . pSQL($value['id_order']) . "', 
                    '" . pSQL($value['id_order_invoice']) . "',
                    '" . pSQL($value['number']) . "',
                    '" . pSQL($value['delivery_number']) . "',
                    '" . pSQL($value['delivery_date']) . "',
                    '" . pSQL($value['total_discount_tax_incl']) . "',
                    '" . pSQL($value['total_discount_tax_excl']) . "',
                    '" . pSQL($value['total_paid_tax_incl']) . "',
                    '" . pSQL($value['total_paid_tax_excl']) . "',
                    '" . pSQL($value['total_products']) . "',
                    '" . pSQL($value['total_products_wt']) . "',
                    '" . pSQL($value['total_shipping_tax_incl']) . "',
                    '" . pSQL($value['total_shipping_tax_excl']) . "',
                    '" . pSQL($value['total_wrapping_tax_incl']) . "',
                    '" . pSQL($value['total_wrapping_tax_excl']) . "',
                    '" . pSQL($value['shipping_tax_computation_method']) . "',
                    '" . pSQL($value['note']) . "',
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

    private function populateOrderInvoicePayment($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "order_invoice_payment WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."order_invoice_payment WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "order_invoice_payment (
                    `id_order`, 
                    `id_order_invoice`,
                    `id_order_payment`
                ) 
                VALUES (
                    '" . pSQL($value['id_order']) . "', 
                    '" . pSQL($value['id_order_invoice']) . "',
                    '" . pSQL($value['id_order_payment']) . "'
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

    private function populateOrderMessage($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "order_message WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."order_message WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "order_message (
                    `id_order_message`, 
                    `date_add`
                ) 
                VALUES (
                    '" . pSQL($value['id_order_message']) . "', 
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


    private function populateOrderMessageLang($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "order_message_lang WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."order_message_lang WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "order_message_lang (
                    `id_order_message`, 
                    `name`,
                    `id_lang`,
                    `message`
                ) 
                VALUES (
                    '" . pSQL($value['id_order_message']) . "', 
                    '" . pSQL($value['name']) . "',
                    '" . pSQL($value['id_lang']) . "',
                    '" . pSQL($value['message']) . "'
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


    private function populateOrderPayment($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "order_payment WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."order_payment WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "order_payment (
                    `id_order_payment`,
                    `order_reference`,
                    `id_currency`,
                    `amount`,
                    `payment_method`,
                    `conversion_rate`,
                    `transaction_id`,
                    `card_number`,
                    `card_brand`,
                    `card_expiration`,
                    `card_holder`,
                    `date_add`
                ) 
                VALUES (
                    '" . pSQL($value['id_order_payment']) . "',
                    '" . pSQL($value['order_reference']) . "',
                    '" . pSQL($value['id_currency']) . "',
                    '" . pSQL($value['amount']) . "',
                    '" . pSQL($value['payment_method']) . "',
                    '" . pSQL($value['conversion_rate']) . "',
                    '" . pSQL($value['transaction_id']) . "',
                    '" . pSQL($value['card_number']) . "',
                    '" . pSQL($value['card_brand']) . "',
                    '" . pSQL($value['card_expiration']) . "',
                    '" . pSQL($value['card_holder']) . "',
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

    private function populateOrderReturn($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "order_return WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."order_return WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "order_return (
                    `id_order_return`, 
                    `id_customer`,
                    `id_order`,
                    `state`,
                    `question`,
                    `date_add`,
                    `date_upd`
                ) 
                VALUES (
                    '" . pSQL($value['id_order_return']) . "', 
                    '" . pSQL($value['id_customer']) . "',
                    '" . pSQL($value['id_order']) . "',
                    '" . pSQL($value['state']) . "',
                    '" . pSQL($value['question']) . "',
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

    private function populateOrderReturnDetail($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "order_return_detail WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."order_return_detail WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "order_return_detail (
                    `id_order_return`, 
                    `id_order_detail`,
                    `id_customization`,
                    `product_quantity`
                ) 
                VALUES (
                    '" . pSQL($value['id_order_return']) . "', 
                    '" . pSQL($value['id_order_detail']) . "',
                    '" . pSQL($value['id_customization']) . "',
                    '" . pSQL($value['product_quantity']) . "'
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

    private function populateOrderReturnState($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "order_return_state WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."order_return_state WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "order_return_state (
                    `id_order_return_state`, 
                    `color`
                ) 
                VALUES (
                    '" . pSQL($value['id_order_return_state']) . "', 
                    '" . pSQL($value['color']) . "'
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

    private function populateOrderReturnLang($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "order_return_state_lang WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."order_return_state_lang WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "order_return_state_lang (
                    `id_order_return_state`, 
                    `name`,
                    `id_lang`
                ) 
                VALUES (
                    '" . pSQL($value['id_order_return_state']) . "', 
                    '" . pSQL($value['name']) . "',
                    '" . pSQL($value['id_lang']) . "'
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

    private function populateOrderSlip($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "order_slip WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."order_slip WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "order_slip (
                    `id_order_slip`, 
                    `conversion_rate`,
                    `id_customer`,
                    `id_order`,
                    `shipping_cost`,
                    `amount`,
                    `shipping_cost_amount`,
                    `partial`,
                    `date_add`,
                    `date_upd`
                ) 
                VALUES (
                    '" . pSQL($value['id_order_slip']) . "', 
                    '" . pSQL($value['conversion_rate']) . "',
                    '" . pSQL($value['id_customer']) . "',
                    '" . pSQL($value['id_order']) . "',
                    '" . pSQL($value['shipping_cost']) . "',
                    '" . pSQL($value['amount']) . "',
                    '" . pSQL($value['shipping_cost_amount']) . "',
                    '" . pSQL($value['partial']) . "',
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

    private function populateOrderSlipDetail($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "order_slip_detail WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."order_slip_detail WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "order_slip_detail (
                    `id_order_slip`, 
                    `id_order_detail`,
                    `product_quantity`,
                    `amount_tax_incl`,
                    `amount_tax_excl`
                ) 
                VALUES (
                    '" . pSQL($value['id_order_slip']) . "', 
                    '" . pSQL($value['id_order_detail']) . "',
                    '" . pSQL($value['product_quantity']) . "',
                    '" . pSQL($value['amount_tax_incl']) . "',
                    '" . pSQL($value['amount_tax_excl']) . "'
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

    private function populateOrderState($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "order_state WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."order_state WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "order_state (
                    `id_order_state`, 
                    `invoice`,
                    `send_email`,
                    `module_name`,
                    `color`,
                    `unremovable`,
                    `hidden`,
                    `logable`,
                    `delivery`,
                    `shipped`,
                    `paid`,
                    `pdf_invoice`,
                    `pdf_delivery`,
                    `deleted`
                ) 
                VALUES (
                    '" . pSQL($value['id_order_state']) . "', 
                    '" . pSQL($value['invoice']) . "',
                    '" . pSQL($value['send_email']) . "',
                    '" . pSQL($value['module_name']) . "',
                    '" . pSQL($value['color']) . "',
                    '" . pSQL($value['unremovable']) . "',
                    '" . pSQL($value['hidden']) . "',
                    '" . pSQL($value['logable']) . "',
                    '" . pSQL($value['delivery']) . "',
                    '" . pSQL($value['shipped']) . "',
                    '" . pSQL($value['paid']) . "',
                    '" . pSQL($value['pdf_invoice']) . "',
                    '" . pSQL($value['pdf_delivery']) . "',
                    '" . pSQL($value['deleted']) . "'
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

    private function populateOrderStateLang($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "order_state_lang WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."order_state_lang WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "order_state_lang (
                    `id_order_state`, 
                    `id_lang`,
                    `name`,
                    `template`
                ) 
                VALUES (
                    '" . pSQL($value['id_order_state']) . "', 
                    '" . pSQL($value['id_lang']) . "',
                    '" . pSQL($value['name']) . "',
                    '" . pSQL($value['template']) . "'
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
    
    


    public function populateAllOrders($conn, $prefix) {
        $this->populateOrders($conn,$prefix);
        $this->populateOrderDetail($conn,$prefix);
        $this->populateOrderInvoice($conn,$prefix);
        $this->populateOrderMessageLang($conn,$prefix);
        $this->populateOrderMessage($conn,$prefix);
        $this->populateOrderInvoicePayment($conn,$prefix);
        $this->populateOrderPayment($conn,$prefix);
        $this->populateOrderReturn($conn,$prefix);
        $this->populateOrderReturnLang($conn,$prefix);
        $this->populateOrderSlip($conn,$prefix);
        $this->populateOrderSlipDetail($conn,$prefix);
        $this->populateOrderState($conn,$prefix);
        $this->populateOrderStateLang($conn,$prefix);
        $this->populateOrderReturnState($conn,$prefix);
        $this->populateOrderReturnDetail($conn,$prefix);
        $this->populateOrderHistory($conn,$prefix);
       
    }
}