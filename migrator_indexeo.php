<?php
/**
* 2007-2024 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2024 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once('/functions/employeePopulator.php');
require_once('/functions/customerPopulator.php');

class Migrator_indexeo extends Module
{
    

    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'migrator_indexeo';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Hector Valls';
        $this->need_instance = 1;
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('Modulo de Migracion');
        $this->description = $this->l('Modulo para migrar prestashops con versiones antiguas, a las mas recientes');
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('displayBackOfficeHeader');
    }

    public function uninstall()
    {
        return parent::uninstall();
    }

    public function getContent()
    {
        if (((bool)Tools::isSubmit('submitMigrator_indexeoModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        //$output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $this->renderForm();
    }

    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitMigrator_indexeoModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Configurar conexión'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'name' => 'OLD_DB_HOST',
                        'label' => $this->l('Host antiguo'),
                    ),
                    array(
                        'type' => 'text',
                        'name' => 'OLD_DB_USERNAME',
                        'label' => $this->l('Usuario antiguo'),
                    ),
                    array(
                        'type' => 'password',
                        'name' => 'OLD_DB_PASSWORD',
                        'label' => $this->l('Contraseña antigua'),
                    ),
                    array(
                        'type' => 'text',
                        'name' => 'OLD_DB',
                        'label' => $this->l('Base de datos antigua'),
                    ),
                    array(
                        'type' => 'text',
                        'name' => 'OLD_DB_PREFIX',
                        'label' => $this->l('Prefijo de las tablas'),
                    ),
                    array(
                        'type' => 'text',
                        'name' => 'OLD_COOKIE_KEY',
                        'label' => $this->l('Cookie key antigua'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Guardar'),
                ),
            ),
        );
    }

    protected function getConfigFormValues()
    {
        return array(
            'OLD_DB_HOST' => Configuration::get('OLD_DB_HOST', 'localhost'),
            'OLD_DB_USERNAME' => Configuration::get('OLD_DB_USERNAME', 'admin'),
            'OLD_DB_PASSWORD' => Configuration::get('OLD_DB_PASSWORD', null),
            'OLD_DB' => Configuration::get('OLD_DB', null),
            'OLD_DB_PREFIX' => Configuration::get('OLD_DB_PREFIX', null),
            'OLD_COOKIE_KEY' => Configuration::get('OLD_COOKIE_KEY', ''),
        );
    }


    protected function postProcess()
    {
        $this->form_values = $this->getConfigFormValues();

        foreach (array_keys($this->form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
        $this->cambiarCookieKey();
        if($this->testConnection()) {
            $host = $this->form_values['OLD_DB_HOST'];
            $username = $this->form_values['OLD_DB_USERNAME'];
            $password = $this->form_values['OLD_DB_PASSWORD'];
            $dbname = $this->form_values['OLD_DB'];
            $prefix = $this->form_values['OLD_DB_PREFIX'];

            try {

                $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                //  EMPLOYEES----------------------------------------------
                $employeePopulator = new EmploeePopulator();
                $employeePopulator->populateAllEmployees($conn, $prefix);


                //  CUSTOMERS-----------------------------------------------
                $customerPopulator = new CustomerPopulator();
                $customerPopulator->populateAllCustomers($conn, $prefix);
                


                //  CATEGORIES-----------------------------------------------
                $this->populateCategory($conn,$prefix);
                $this->populateCategoryGroup($conn,$prefix);
                $this->populateCategoryLang($conn,$prefix);
                $this->populateCategoryShop($conn,$prefix);
                $this->populateCMSCategory($conn,$prefix);
                $this->populateCMSCategoryLang($conn,$prefix);
                $this->populateCMSCategoryShop($conn,$prefix);
                $this->popullateLayeredCategory($conn,$prefix);
                $this->populateCategoryProduct($conn,$prefix);


                // PRODUCTS--------------------------------------------------
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


                //  COMMENTS (these tables ain't on the 1.6)

                // $this->populateProductComment($conn,$prefix);
                // $this->populateProductCommentCriterion($conn,$prefix);
                //$this->populateProductCommentCriterionCategory($conn,$prefix);
                //$this->populateProductCommentCriterionLang($conn,$prefix);
                //$this->populateProductCommentCriterionProduct($conn,$prefix);
                //$this->populateProductCommentCriterionGrade($conn,$prefix);
                //$this->populateProductCommentRepeat($conn,$prefix);
                //$this->populateProductCommentUsefulness($conn,$prefix);



                //  GROUP---------------------------------------------------------
                $this->populateGroup($conn,$prefix);
                $this->populateAttributeGroup($conn,$prefix);
                $this->populateAttributeGroupLang($conn,$prefix);
                $this->populateAttributeGroupShop($conn,$prefix);
                $this->populateCarrierGroup($conn,$prefix);
                $this->populateGroupLang($conn,$prefix);
                $this->populateGroupReduction($conn,$prefix);
                $this->populateGroupShop($conn,$prefix);
                $this->populateLayeredIndexableAttributeGroup($conn,$prefix);
                $this->populateLayeredIndexableAttributeGroupLangValue($conn,$prefix);
                $this->populateShopGroup($conn,$prefix);
                $this->populateSpecificPriceRuleConditionGroup($conn,$prefix);
                $this->populateTaxRulesGroup($conn,$prefix);
                $this->populateTaxRulesGroupShop($conn,$prefix);



                //  ATTRIBUTE---------------------------------------------------------
                $this->populateAttribute($conn,$prefix);
                $this->populateAttributeShop($conn,$prefix);
                $this->populateAttributeLang($conn,$prefix);
                $this->populateLayeredIndexableAttributeLangValue($conn,$prefix);


                //  CARRIER---------------------------------------------------------
                $this->populateCarrier($conn,$prefix);
                $this->populateCarrierShop($conn,$prefix);
                $this->populateCarrierLang($conn,$prefix);
                $this->populateCarrierZone($conn,$prefix);
                $this->populateOderCarrier($conn,$prefix);


                // TAX----------------------------------------------------------------------
                $this->populateTax($conn,$prefix);
                $this->populateTaxLang($conn,$prefix);
                $this->populateTaxRule($conn,$prefix);
                $this->populateOrderDetailTax($conn,$prefix);
                $this->populateOrderInvoiceTax($conn,$prefix);


                //MANUFACTURER---------------------------------------------------------------
                $this->populateManufacturer($conn,$prefix);
                $this->populateManufacturerLang($conn,$prefix);
                $this->populateManufacturerShop($conn,$prefix);


                //SUPPLIER-------------------------------------------------------------------
                $this->populateSupplier($conn,$prefix);
                $this->populateSupplierLang($conn,$prefix);
                $this->populateSupplierShop($conn,$prefix);


                //  SUPPLY--------------------------------------------------------------------
                $this->populateSupplyOrder($conn,$prefix);
                $this->populateSupplyOrderHistory($conn,$prefix);
                $this->populateSupplyOrderDetail($conn,$prefix);
                $this->populateSupplyOrderReceiptHistory($conn,$prefix);
                $this->populateSupplyOrderState($conn,$prefix);
                $this->populateSupplyOrderStateLang($conn,$prefix);



                //  ATTACHMENT------------------------------------------------------------------
                $this->populateAttachment($conn,$prefix);
                $this->populateAttachmentLang($conn,$prefix);


                // ADDRESS-----------------------------------------------------------------------
                $this->populateAddress($conn,$prefix);
                $this->populateAddressFormat($conn,$prefix);


                //  DELIVERY---------------------------------------------------------------------
                $this->populateDelivery($conn,$prefix);


                // WAREHOUSE---------------------------------------------------------------------
                $this->populateWarehouse($conn,$prefix);
                $this->populateWarehouseShop($conn,$prefix);


                // CMS---------------------------------------------------------------------------
                $this->populateCMS($conn,$prefix);
                $this->populateCMSLang($conn,$prefix);
                $this->populateCMSShop($conn,$prefix);


                //  IMAGE------------------------------------------------------------------------
                $this->populateImage($conn,$prefix);
                $this->populateImageLang($conn,$prefix);
                $this->populateImageShop($conn,$prefix);
                $this->populateImageType($conn,$prefix);

                // ORDER--------------------------------------------------------------------------
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
            catch(PDOException $exception) {
                echo "Error: " . $exception->getMessage();
            }
            // Cerrar conexion
            $conn = null;
        }
    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookDisplayBackOfficeHeader()
    {
        if (Tools::getValue('configure') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }


    private function cambiarCookieKey(){
        $archivo = _PS_ROOT_DIR_.'/app/config/parameters.php';
        $newCookieKey = $this->form_values['OLD_COOKIE_KEY'];

        $nueva_linea = "    'cookie_key' => '". $this->form_values['OLD_COOKIE_KEY'] ."',\n"; 

        if ($gestor = fopen($archivo, 'r+')) {
            while (!feof($gestor)) {
                $linea = fgets($gestor);
                if (strpos($linea, 'cookie_key') !== false) {
                    fseek($gestor, -strlen($linea), SEEK_CUR);
                    fwrite($gestor, $nueva_linea);
                    break;
                }
            }
            fclose($gestor);
            echo "La línea que contiene 'cookie_key' ha sido modificada.";
        } else {
            echo "No se pudo abrir el archivo.";
        }
    }

    
    
    public function populateCategory($conn, $prefix){
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

    public function populateCategoryGroup($conn, $prefix){
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
    
    public function populateCategoryLang($conn, $prefix){
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

    public function populateCategoryProduct($conn, $prefix){
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

    public function populateCategoryShop($conn, $prefix){
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

    public function populateCMSCategory($conn, $prefix){
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

    public function populateCMSCategoryLang($conn, $prefix){
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

    public function populateCMSCategoryShop($conn, $prefix){
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

    public function popullateLayeredCategory($conn, $prefix){
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


    public function populateProductLang($conn, $prefix){
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


    public function populateProductShop($conn, $prefix){
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

    public function populateFeatureProduct($conn, $prefix){
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

    public function populateLayeredProductAttribute($conn, $prefix){
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

    public function populateProductAttachment($conn, $prefix){
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
    
    public function populateProductAttribute($conn, $prefix){
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

    public function populateProductAttributeCombination($conn, $prefix){
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
    public function populateProductAttributeImage($conn, $prefix){
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

    public function populateProductAttributeShop($conn, $prefix){
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

    public function populateProductCarrier($conn, $prefix){
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
    
    public function populateProductCountryTax($conn, $prefix){
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

    public function populateProductDownload($conn, $prefix){
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

    public function populateProductSale($conn, $prefix){
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

    public function populateProductSupplier($conn, $prefix){
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

    public function populateProductTag($conn, $prefix){
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

    public function populateWareHouseProductLocation($conn, $prefix){
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


    public function populateProductComment($conn, $prefix){
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

    public function populateProductCommentCriterion($conn, $prefix){
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

    public function populateProductCommentCriterionCategory($conn, $prefix){
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


    public function populateProductCommentCriterionLang($conn, $prefix){
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

    public function populateProductCommentCriterionProduct($conn, $prefix){
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

    public function populateProductCommentCriterionGrade($conn, $prefix){
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

    public function populateProductCommentRepeat($conn, $prefix){
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

    public function populateProductCommentUsefulness($conn, $prefix){
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

    public function populateAttributeGroup($conn, $prefix){
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

    public function populateAttributeGroupLang($conn, $prefix){
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

    public function populateAttributeGroupShop($conn, $prefix){
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

    public function populateCarrierGroup($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "carrier_group WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."carrier_group WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "carrier_group (
                    `id_carrier`, 
                    `id_group`
                ) 
                VALUES (
                    '" . pSQL($value['id_carrier']) . "', 
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
    
    public function populateGroup($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "group WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."group WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "group (
                    `id_group`, 
                    `reduction`, 
                    `price_display_method`,
                    `show_prices`,
                    `date_add`,
                    `date_upd`
                ) 
                VALUES (
                    '" . pSQL($value['id_group']) . "', 
                    '" . pSQL($value['reduction']) . "', 
                    '" . pSQL($value['price_display_method']) . "',
                    '" . pSQL($value['show_prices']) . "',
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

    public function populateGroupLang($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "group_lang WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."group_lang WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "group_lang (
                    `id_group`, 
                    `id_lang`, 
                    `name`
                ) 
                VALUES (
                    '" . pSQL($value['id_group']) . "', 
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

    public function populateGroupReduction($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "group_reduction WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."group_reduction WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "group_reduction (
                    `id_group`, 
                    `id_group_reduction`, 
                    `id_category`,
                    `reduction`
                ) 
                VALUES (
                    '" . pSQL($value['id_group']) . "', 
                    '" . pSQL($value['id_group_reduction']) . "', 
                    '" . pSQL($value['id_category']) . "',
                    '" . pSQL($value['reduction']) . "'

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

    public function populateGroupShop($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "group_shop WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."group_shop WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "group_shop (
                    `id_group`, 
                    `id_shop`
                ) 
                VALUES (
                    '" . pSQL($value['id_group']) . "', 
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

    public function populateLayeredIndexableAttributeGroup($conn, $prefix){
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

    public function populateLayeredIndexableAttributeGroupLangValue($conn, $prefix){
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

    public function populateShopGroup($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "shop_group WHERE 1");
            $query->execute();

            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "shop_group (
                    `id_shop_group`, 
                    `name`,
                    `share_customer`,
                    `share_stock`,
                    `active`,
                    `deleted`,
                    `color`
                ) 
                VALUES (
                    '" . pSQL($value['id_shop_group']) . "', 
                    '" . pSQL($value['name']) . "',
                    '" . pSQL($value['share_customer']) . "',
                    '" . pSQL($value['share_stock']) . "',
                    '" . pSQL($value['active']) . "',
                    '" . pSQL($value['deleted']) . "',
                    0
                )
                ON DUPLICATE KEY UPDATE 
                    id_shop_group = VALUES(id_shop_group), 
                    name = VALUES(name), 
                    share_customer = VALUES(share_customer), 
                    share_stock = VALUES(share_stock), 
                    active = VALUES(active), 
                    deleted = VALUES(deleted);";
                Db::getInstance()->execute($sql);
            }
        }
        catch(PDOException $exception) {
            echo "Error: " . $exception->getMessage();
        }
        // Cerrar conexion
        $conn = null;
    }

    public function populateSpecificPriceRuleConditionGroup($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "specific_price_rule_condition_group WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."specific_price_rule_condition_group WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "specific_price_rule_condition_group (
                    `id_specific_price_rule_condition_group`, 
                    `id_specific_price_rule`
                ) 
                VALUES (
                    '" . pSQL($value['id_specific_price_rule_condition_group']) . "', 
                    '" . pSQL($value['id_specific_price_rule']) . "'
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

    public function populateTaxRulesGroup($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "tax_rules_group WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."tax_rules_group WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "tax_rules_group (
                    `id_tax_rules_group`, 
                    `name`,
                    `active`,
                    `deleted`,
                    `date_add`,
                    `date_upd`
                ) 
                VALUES (
                    '" . pSQL($value['id_tax_rules_group']) . "', 
                    '" . pSQL($value['name']) . "',
                    '" . pSQL($value['active']) . "',
                    0,
                    '" . pSQL(date("Y-m-d H:i:s")) . "',
                    '" . pSQL(date("Y-m-d H:i:s")) . "'
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

    public function populateTaxRulesGroupShop($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "tax_rules_group_shop WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."tax_rules_group_shop WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "tax_rules_group_shop (
                    `id_tax_rules_group`, 
                    `id_shop`
                ) 
                VALUES (
                    '" . pSQL($value['id_tax_rules_group']) . "', 
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

    public function populateAttribute($conn, $prefix){
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

    public function populateAttributeShop($conn, $prefix){
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

    public function populateLayeredIndexableAttributeLangValue($conn, $prefix){
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

    public function populateAttributeLang($conn, $prefix){
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


    public function populateCarrier($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "carrier WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."carrier WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "carrier (
                    `id_carrier`,
                    `id_reference`,
                    `name`, 
                    `url`,
                    `active`,
                    `deleted`,
                    `shipping_handling`,
                    `range_behavior`,
                    `is_module`,
                    `is_free`,
                    `shipping_external`,
                    `need_range`,
                    `external_module_name`,
                    `shipping_method`,
                    `position`,
                    `max_width`,
                    `max_height`,
                    `max_depth`,
                    `grade`
                ) 
                VALUES (
                    '" . pSQL($value['id_carrier']) . "',
                    '" . pSQL($value['id_reference']) . "',
                    '" . pSQL($value['name']) . "', 
                    '" . pSQL($value['url']) . "',
                    '" . pSQL($value['active']) . "',
                    '" . pSQL($value['deleted']) . "',
                    '" . pSQL($value['shipping_handling']) . "',
                    '" . pSQL($value['range_behavior']) . "',
                    '" . pSQL($value['is_module']) . "',
                    '" . pSQL($value['is_free']) . "',
                    '" . pSQL($value['shipping_external']) . "',
                    '" . pSQL($value['need_range']) . "',
                    '" . pSQL($value['external_module_name']) . "',
                    '" . pSQL($value['shipping_method']) . "',
                    '" . pSQL($value['position']) . "',
                    '" . pSQL($value['max_width']) . "',
                    '" . pSQL($value['max_height']) . "',
                    '" . pSQL($value['max_depth']) . "',
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


    public function populateCarrierShop($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "carrier_shop WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."carrier_shop WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "carrier_shop (
                    `id_carrier`,
                    `id_shop`
                ) 
                VALUES (
                    '" . pSQL($value['id_carrier']) . "',
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

    public function populateCarrierLang($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "carrier_lang WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."carrier_lang WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "carrier_lang (
                    `id_carrier`,
                    `id_shop`,
                    `id_lang`, 
                    `delay`
                ) 
                VALUES (
                    '" . pSQL($value['id_carrier']) . "',
                    '" . pSQL($value['id_shop']) . "',
                    '" . pSQL($value['id_lang']) . "', 
                    '" . pSQL($value['delay']) . "'

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

    public function populateCarrierZone($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "carrier_zone WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."carrier_zone WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "carrier_zone (
                    `id_carrier`,
                    `id_zone`
                ) 
                VALUES (
                    '" . pSQL($value['id_carrier']) . "',
                    '" . pSQL($value['id_zone']) . "'

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

    public function populateOderCarrier($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "order_carrier WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."order_carrier WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "order_carrier (
                    `id_order_carrier`,
                    `id_order`,
                    `id_order_invoice`, 
                    `weight`,
                    `shipping_cost_tax_excl`,
                    `shipping_cost_tax_incl`,
                    `tracking_number`,
                    `date_add`,
                    `id_carrier`
                ) 
                VALUES (
                    '" . pSQL($value['id_order_carrier']) . "',
                    '" . pSQL($value['id_order']) . "',
                    '" . pSQL($value['id_order_invoice']) . "', 
                    '" . pSQL($value['weight']) . "',
                    '" . pSQL($value['shipping_cost_tax_excl']) . "',
                    '" . pSQL($value['shipping_cost_tax_incl']) . "',
                    '" . pSQL($value['tracking_number']) . "',
                    '" . pSQL($value['date_add']) . "',
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

    public function populateTax($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "tax WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."tax WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "tax (
                    `id_tax`,
                    `rate`,
                    `active`,
                    `deleted`
                ) 
                VALUES (
                    '" . pSQL($value['id_tax']) . "',
                    '" . pSQL($value['rate']) . "',
                    '" . pSQL($value['active']) . "',
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

    public function populateTaxLang($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "tax_lang WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."tax_lang WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "tax_lang (
                    `id_tax`,
                    `id_lang`,
                    `name`
                ) 
                VALUES (
                    '" . pSQL($value['id_tax']) . "',
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

    public function populateTaxRule($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "tax_rule WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."tax_rule WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "tax_rule (
                    `id_tax_rule`,
                    `id_tax_rules_group`, 
                    `id_country`,
                    `id_state`,
                    `zipcode_from`,
                    `zipcode_to`,
                    `id_tax`,
                    `behavior`,
                    `description`
                ) 
                VALUES (
                    '" . pSQL($value['id_tax_rule']) . "',
                    '" . pSQL($value['id_tax_rules_group']) . "', 
                    '" . pSQL($value['id_country']) . "',
                    '" . pSQL($value['id_state']) . "',
                    '" . pSQL($value['zipcode_from']) . "',
                    '" . pSQL($value['zipcode_to']) . "',
                    '" . pSQL($value['id_tax']) . "',
                    '" . pSQL($value['behavior']) . "',
                    '" . pSQL($value['description']) . "'

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

    public function populateOrderDetailTax($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "order_detail_tax WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."order_detail_tax WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "order_detail_tax (
                    `id_order_detail`,
                    `id_tax`, 
                    `unit_amount`,
                    `total_amount`
                ) 
                VALUES (
                    '" . pSQL($value['id_order_detail']) . "',
                    '" . pSQL($value['id_tax']) . "', 
                    '" . pSQL($value['unit_amount']) . "',
                    '" . pSQL($value['total_amount']) . "'
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

    public function populateOrderInvoiceTax($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "order_invoice_tax WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."order_invoice_tax WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "order_invoice_tax (
                    `id_order_invoice`,
                    `type`,
                    `id_tax`,
                    `amount`
                ) 
                VALUES (
                    '" . pSQL($value['id_order_invoice']) . "',
                    '" . pSQL($value['type']) . "',
                    '" . pSQL($value['id_tax']) . "',
                    '" . pSQL($value['amount']) . "'

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

    public function populateSupplier($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "supplier WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."supplier WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "supplier (
                    `id_supplier`,
                    `name`, 
                    `date_add`,
                    `date_upd`,
                    `active`
                ) 
                VALUES (
                    '" . pSQL($value['id_supplier']) . "',
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


    public function populateSupplierLang($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "supplier_lang WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."supplier_lang WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "supplier_lang (
                    `id_supplier`,
                    `id_lang`, 
                    `description`,
                    `meta_title`,
                    `meta_keywords`,
                    `meta_description`
                ) 
                VALUES (
                    '" . pSQL($value['id_supplier']) . "',
                    '" . pSQL($value['id_lang']) . "', 
                    '" . pSQL($value['description']) . "',
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

    public function populateSupplierShop($conn, $prefix){
        try {   
            $query = $conn->prepare("SELECT * FROM " .$prefix. "supplier_shop WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."supplier_shop WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "supplier_shop (
                    `id_supplier`,
                    `id_shop`
                ) 
                VALUES (
                    '" . pSQL($value['id_supplier']) . "',
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


    public function populateSupplyOrder($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "supply_order WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."supply_order WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "supply_order (
                    `id_supply_order`, 
                    `id_supplier`,
                    `supplier_name`,
                    `id_lang`,
                    `id_warehouse`,
                    `id_supply_order_state`,
                    `id_currency`,
                    `id_ref_currency`,
                    `reference`,
                    `date_add`,
                    `date_upd`,
                    `date_delivery_expect`,
                    `total_te`,
                    `total_with_discount_te`,
                    `total_tax`,
                    `total_ti`,
                    `discount_rate`,
                    `discount_value_te`,
                    `is_template`
                ) 
                VALUES (
                    '" . pSQL($value['id_supply_order']) . "', 
                    '" . pSQL($value['id_supplier']) . "',
                    '" . pSQL($value['supplier_name']) . "',
                    '" . pSQL($value['id_lang']) . "',
                    '" . pSQL($value['id_warehouse']) . "',
                    '" . pSQL($value['id_supply_order_state']) . "',
                    '" . pSQL($value['id_currency']) . "',
                    '" . pSQL($value['id_ref_currency']) . "',
                    '" . pSQL($value['reference']) . "',
                    '" . pSQL($value['date_add']) . "',
                    '" . pSQL($value['date_upd']) . "',
                    '" . pSQL($value['date_delivery_expect']) . "',
                    '" . pSQL($value['total_te']) . "',
                    '" . pSQL($value['total_with_discount_te']) . "',
                    '" . pSQL($value['total_tax']) . "',
                    '" . pSQL($value['total_ti']) . "',
                    '" . pSQL($value['discount_rate']) . "',
                    '" . pSQL($value['discount_value_te']) . "',
                    '" . pSQL($value['is_template']) . "'
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

    public function populateSupplyOrderHistory($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "supply_order_history WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."supply_order_history WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "supply_order_history (
                    `id_supply_order_history`, 
                    `id_supply_order`,
                    `id_employee`,
                    `employee_lastname`,
                    `employee_firstname`,
                    `id_state`,
                    `date_add`
                ) 
                VALUES (
                    '" . pSQL($value['id_supply_order_history']) . "', 
                    '" . pSQL($value['id_supply_order']) . "',
                    '" . pSQL($value['id_employee']) . "',
                    '" . pSQL($value['employee_lastname']) . "',
                    '" . pSQL($value['employee_firstname']) . "',
                    '" . pSQL($value['id_state']) . "',
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

    public function populateSupplyOrderDetail($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "supply_order_detail WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."supply_order_detail WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "supply_order_detail (
                    `id_supply_order_detail`, 
                    `id_supply_order`,
                    `id_currency`,
                    `id_product`,
                    `id_product_attribute`,
                    `reference`,
                    `supplier_reference`,
                    `name`,
                    `ean13`,
                    `isbn`,
                    `upc`,
                    `npm`,
                    `exchange_rate`,
                    `unit_price_te`,
                    `quantity_expected`,
                    `quantity_received`,
                    `price_te`,
                    `discount_rate`,
                    
                    `discount_value_te`,
                    `price_with_discount_te`,
                    `tax_rate`,
                    `tax_value`,
                    `total_ti`,
                    `tax_value_with_order_discount`,
                    `price_value_with_order_discount_te`
                ) 
                VALUES (
                    '" . pSQL($value['id_supply_order_detail']) . "', 
                    '" . pSQL($value['id_supply_order']) . "',
                    '" . pSQL($value['id_currency']) . "',
                    '" . pSQL($value['id_product']) . "',
                    '" . pSQL($value['id_product_attribute']) . "',
                    '" . pSQL($value['reference']) . "',
                    '" . pSQL($value['supplier_reference']) . "',
                    '" . pSQL($value['name']) . "',
                    '" . pSQL($value['ean13']) . "',
                    '" . pSQL($value['isbn']) . "',
                    '" . pSQL($value['upc']) . "',
                    '" . pSQL($value['npm']) . "',
                    '" . pSQL($value['exchange_rate']) . "',
                    '" . pSQL($value['unit_price_te']) . "',
                    '" . pSQL($value['quantity_expected']) . "',
                    '" . pSQL($value['quantity_received']) . "',
                    '" . pSQL($value['price_te']) . "',
                    '" . pSQL($value['discount_rate']) . "',

                    '" . pSQL($value['discount_value_te']) . "',
                    '" . pSQL($value['price_with_discount_te']) . "',
                    '" . pSQL($value['tax_rate']) . "',
                    '" . pSQL($value['tax_value']) . "',
                    '" . pSQL($value['total_ti']) . "',
                    '" . pSQL($value['tax_value_with_order_discount']) . "',
                    '" . pSQL($value['price_value_with_order_discount_te']) . "'
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

    public function populateSupplyOrderReceiptHistory($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "supply_order_receipt_history WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."supply_order_receipt_history WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "supply_order_receipt_history (
                    `id_supply_order_receipt_history`, 
                    `id_sypply_order_detail`,
                    `id_employee`,
                    `employee_lastname`,
                    `employee_firstname`,
                    `id_supply_order_state`,
                    `quantity`,
                    `date_add`
                ) 
                VALUES (
                    '" . pSQL($value['id_supply_order_receipt_history']) . "', 
                    '" . pSQL($value['id_sypply_order_detail']) . "',
                    '" . pSQL($value['id_employee']) . "',
                    '" . pSQL($value['employee_lastname']) . "',
                    '" . pSQL($value['employee_firstname']) . "',
                    '" . pSQL($value['id_supply_order_state']) . "',
                    '" . pSQL($value['quantity']) . "',
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

    public function populateSupplyOrderState($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "supply_order_state WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."supply_order_state WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "supply_order_state (
                    `id_supply_order_state`, 
                    `delivery_note`,
                    `editable`,
                    `receipt_state`,
                    `pending_receipt`,
                    `enclosed`,
                    `color`
                ) 
                VALUES (
                    '" . pSQL($value['id_supply_order_state']) . "', 
                    '" . pSQL($value['delivery_note']) . "',
                    '" . pSQL($value['editable']) . "',
                    '" . pSQL($value['receipt_state']) . "',
                    '" . pSQL($value['pending_receipt']) . "',
                    '" . pSQL($value['enclosed']) . "',
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

    public function populateSupplyOrderStateLang($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "supply_order_state_lang WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."supply_order_state_lang WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "supply_order_state_lang (
                    `id_supply_order_state`, 
                    `id_lang`,
                    `name`
                ) 
                VALUES (
                    '" . pSQL($value['id_supply_order_state']) . "', 
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

    public function populateAttachment($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "attachment WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."attachment WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "attachment (
                    `id_attachment`, 
                    `file`,
                    `file_name`,
                    `file_size`,
                    `mime`
                ) 
                VALUES (
                    '" . pSQL($value['id_attachment']) . "', 
                    '" . pSQL($value['file']) . "',
                    '" . pSQL($value['file_name']) . "',
                    '" . pSQL($value['file_size']) . "',
                    '" . pSQL($value['mime']) . "'
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

    public function populateAttachmentLang($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "attachment_lang WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."attachment_lang WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "attachment_lang (
                    `id_attachment`, 
                    `name`,
                    `description`,
                    `id_lang`
                ) 
                VALUES (
                    '" . pSQL($value['id_attachment']) . "', 
                    '" . pSQL($value['name']) . "',
                    '" . pSQL($value['description']) . "',
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

    public function populateAddress($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "address WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."address WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "address (
                    `id_address`, 
                    `id_country`,
                    `id_state`,
                    `id_customer`,
                    `id_manufacturer`,
                    `id_supplier`,
                    `id_warehouse`,
                    `alias`,
                    `lastname`,
                    `firstname`,
                    `address1`,
                    `address2`,
                    `postcode`,
                    `city`,
                    `other`,
                    `phone`,
                    `phone_mobile`,
                    `vat_number`,
                    `dni`,
                    `date_add`,
                    `date_upd`,
                    `active`,
                    `deleted`,
                    `company`
                ) 
                VALUES (
                    '" . pSQL($value['id_address']) . "', 
                    '" . pSQL($value['id_country']) . "',
                    '" . pSQL($value['id_state']) . "',
                    '" . pSQL($value['id_customer']) . "',
                    '" . pSQL($value['id_manufacturer']) . "',
                    '" . pSQL($value['id_supplier']) . "',
                    '" . pSQL($value['id_warehouse']) . "',
                    '" . pSQL($value['alias']) . "',
                    '" . pSQL($value['lastname']) . "',
                    '" . pSQL($value['firstname']) . "',
                    '" . pSQL($value['address1']) . "',
                    '" . pSQL($value['address2']) . "',
                    '" . pSQL($value['postcode']) . "',
                    '" . pSQL($value['city']) . "',
                    '" . pSQL($value['other']) . "',
                    '" . pSQL($value['phone']) . "',
                    '" . pSQL($value['phone_mobile']) . "',
                    '" . pSQL($value['vat_number']) . "',
                    '" . pSQL($value['dni']) . "',
                    '" . pSQL($value['date_add']) . "',
                    '" . pSQL($value['date_upd']) . "',
                    '" . pSQL($value['active']) . "',
                    '" . pSQL($value['deleted']) . "',
                    '" . pSQL($value['company']) . "'
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

    public function populateAddressFormat($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "address_format WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."address_format WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "address_format (
                    `id_country`,
                    `format`
                ) 
                VALUES (
                    '" . pSQL($value['id_country']) . "',
                    '" . pSQL($value['format']) . "'
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

    public function populateDelivery($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "delivery WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."delivery WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "delivery (
                    `id_delivery`, 
                    `id_shop`,
                    `id_shop_group`,
                    `id_carrier`,
                    `id_range_price`,
                    `id_range_weight`,
                    `id_zone`,
                    `price`
                ) 
                VALUES (
                    '" . pSQL($value['id_delivery']) . "', 
                    '" . pSQL($value['id_shop']) . "',
                    '" . pSQL($value['id_shop_group']) . "',
                    '" . pSQL($value['id_carrier']) . "',
                    '" . pSQL($value['id_range_price']) . "',
                    '" . pSQL($value['id_range_weight']) . "',
                    '" . pSQL($value['id_zone']) . "',
                    '" . pSQL($value['price']) . "'
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


    public function populateWarehouse($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "warehouse WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."warehouse WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "warehouse (
                    `id_warehouse`, 
                    `id_country`,
                    `id_address`,
                    `id_employee`,
                    `reference`,
                    `name`,
                    `management_type`,
                    `deleted`
                ) 
                VALUES (
                    '" . pSQL($value['id_delivery']) . "', 
                    '" . pSQL($value['id_country']) . "',
                    '" . pSQL($value['id_address']) . "',
                    '" . pSQL($value['id_employee']) . "',
                    '" . pSQL($value['reference']) . "',
                    '" . pSQL($value['name']) . "',
                    '" . pSQL($value['management_type']) . "',
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

    public function populateWarehouseShop($conn, $prefix){
        try {
            $query = $conn->prepare("SELECT * FROM " .$prefix. "warehouse_shop WHERE 1");
            $query->execute();

            Db::getInstance()->execute("DELETE FROM ". _DB_PREFIX_ ."warehouse_shop WHERE 1;");
            foreach($query->fetchAll() as $key=>$value) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "warehouse_shop (
                    `id_warehouse`, 
                    `id_shop`
                ) 
                VALUES (
                    '" . pSQL($value['id_delivery']) . "', 
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

    public function populateCMS($conn, $prefix){
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


    public function populateCMSLang($conn, $prefix){
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

    public function populateCMSShop($conn, $prefix){
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

    public function populateImage($conn, $prefix){
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

    public function populateImageLang($conn, $prefix){
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

    public function populateImageShop($conn, $prefix){
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

    public function populateImageType($conn, $prefix){
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

    public function populateOrders($conn, $prefix){
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

    public function populateOrderDetail($conn, $prefix){
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

    public function populateOrderHistory($conn, $prefix){
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

    public function populateOrderInvoice($conn, $prefix){
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

    public function populateOrderInvoicePayment($conn, $prefix){
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

    public function populateOrderMessage($conn, $prefix){
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


    public function populateOrderMessageLang($conn, $prefix){
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


    public function populateOrderPayment($conn, $prefix){
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

    public function populateOrderReturn($conn, $prefix){
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

    public function populateOrderReturnDetail($conn, $prefix){
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

    public function populateOrderReturnState($conn, $prefix){
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

    public function populateOrderReturnLang($conn, $prefix){
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

    public function populateOrderSlip($conn, $prefix){
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

    public function populateOrderSlipDetail($conn, $prefix){
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

    public function populateOrderState($conn, $prefix){
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

    public function populateOrderStateLang($conn, $prefix){
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

    public function testConnection(){
        $host = $this->form_values['OLD_DB_HOST'];
        $username = $this->form_values['OLD_DB_USERNAME'];
        $password = $this->form_values['OLD_DB_PASSWORD'];
        $dbname = $this->form_values['OLD_DB'];
    
        try {
            $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "Conexión exitosa"; 
            $conn = null; 
            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

}
