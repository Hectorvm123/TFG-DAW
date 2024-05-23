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
require 'functions/addressPopulator.php';
require 'functions/attachmentPopulator.php';
require 'functions/attributePopulator.php';
require 'functions/carrierPopulator.php';
require 'functions/cartPopulator.php';
require 'functions/categoryPopulator.php';
require 'functions/CMSPopulator.php';
require 'functions/customerPopulator.php';
require 'functions/deliveryPopulator.php';
require 'functions/employeePopulator.php';
require 'functions/groupPopulator.php';
require 'functions/imagePopulator.php';
require 'functions/langPopulator.php';
require 'functions/manufacturerPopulator.php';
require 'functions/orderPopulator.php';
require 'functions/productPopulator.php';
require 'functions/stockPopulator.php';
require 'functions/supplierPopulator.php';
require 'functions/supplyPopulator.php';
require 'functions/taxPopulator.php';
require 'functions/warehousePopulator.php';


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
            'OLD_DB_HOST' => Configuration::get('OLD_DB_HOST'),
            'OLD_DB_USERNAME' => Configuration::get('OLD_DB_USERNAME'),
            'OLD_DB_PASSWORD' => Configuration::get('OLD_DB_PASSWORD'),
            'OLD_DB' => Configuration::get('OLD_DB'),
            'OLD_DB_PREFIX' => Configuration::get('OLD_DB_PREFIX'),
            'OLD_COOKIE_KEY' => Configuration::get('OLD_COOKIE_KEY'),
        );
    }


    protected function postProcess()
    {
        $this->form_values = $this->getConfigFormValues();

        foreach (array_keys($this->form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
        $this->form_values = $this->getConfigFormValues();

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
                $employeePopulator = new EmployeePopulator();
                $employeePopulator->populateAllEmployees($conn, $prefix);


                //  CUSTOMERS-----------------------------------------------
                $customerPopulator = new CustomerPopulator();
                $customerPopulator->populateAllCustomers($conn, $prefix);
                


                //  CATEGORIES-----------------------------------------------
                $categoryPopulator = new CategoryPopulator();
                $categoryPopulator->populateAllCategories($conn, $prefix);


                // PRODUCTS--------------------------------------------------
                $productPopulator = new ProductPopulator();
                $productPopulator->populateAllProducts($conn, $prefix);


                //  GROUP---------------------------------------------------------
                $groupsPopulator = new GroupPopulator();
                $groupsPopulator->populateAllGroups($conn, $prefix);


                //  ATTRIBUTE---------------------------------------------------------
                $attributePopulator = new AttributePopulator();
                $attributePopulator->populateAllAttributes($conn, $prefix);


                //  CARRIER---------------------------------------------------------
                $carrierPopulator = new CarrierPopulator();
                $carrierPopulator->populateAllCarriers($conn, $prefix);


                // TAX----------------------------------------------------------------------
                $taxPopulator = new TaxPopulator();
                $taxPopulator->populateAllTaxes($conn, $prefix);


                //  MANUFACTURER---------------------------------------------------------------
                $manufacturerPopulator = new ManufacturerPopulator();
                $manufacturerPopulator->populateAllManufacturers($conn, $prefix);


                //  SUPPLIER-------------------------------------------------------------------
                $supplierPopulator = new SupplierPopulator();
                $supplierPopulator->populateAllSuppliers($conn, $prefix);


                //  SUPPLY--------------------------------------------------------------------
                $supplyPopulator = new SupplyPopulator();
                $supplyPopulator->populateAllSupplies($conn, $prefix);



                //  ATTACHMENT------------------------------------------------------------------
                $attachmentPopulator = new AttachmentPopulator();
                $attachmentPopulator->populateAllAttachments($conn, $prefix);


                // ADDRESS-----------------------------------------------------------------------
                $addressPopulator = new AddressPopulator();
                $addressPopulator->populateAllAddresses($conn, $prefix);


                //  DELIVERY---------------------------------------------------------------------
                $deliveryPopulator = new DeliveryPopulator();
                $deliveryPopulator->populateAllDeliveries($conn, $prefix);


                // WAREHOUSE---------------------------------------------------------------------
                $warehousePopulator = new WarehousePopulator();
                $warehousePopulator->populateAllWarehouses($conn, $prefix);


                // CMS---------------------------------------------------------------------------
                $CMSPopulator = new CMSPopulator();
                $CMSPopulator->populateAllCMS($conn, $prefix);


                //  IMAGE------------------------------------------------------------------------
                $imagePopulator = new ImagePopulator();
                $imagePopulator->populateAllImages($conn, $prefix);

                // ORDER--------------------------------------------------------------------------
                $orderPopulator = new OrderPopulator();
                $orderPopulator->populateAllOrders($conn, $prefix);

                
                //  CART--------------------------------------------------------------------------
                $cartPopulator = new CartPopulator();
                $cartPopulator->populateAllCarts($conn, $prefix);


                //  STOCK-------------------------------------------------------------------------
                $stockPopulator = new StockPopulator();
                $stockPopulator->populateAllStocks($conn, $prefix);
                

                //  LANG-------------------------------------------------------------------------
                $langPopulator = new LangPopulator();
                $langPopulator->populateAllLangs($conn, $prefix);
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


    private function cambiarCookieKey() {
        $archivo = _PS_ROOT_DIR_.'/app/config/parameters.php';
        $nueva_linea = "    'cookie_key' => '". $this->form_values['OLD_COOKIE_KEY'] ."'," . PHP_EOL;
    
        $contenido = file($archivo);
        $linea_modificada = false;
    
        foreach ($contenido as $indice => $linea) {
            if (strpos($linea, 'cookie_key') !== false) {
                $contenido[$indice] = $nueva_linea;
                $linea_modificada = true;
                break;
            }
        }
    
        if ($linea_modificada) {
            file_put_contents($archivo, implode('', $contenido));
            echo "La línea que contiene 'cookie_key' ha sido modificada.";
        } else {
            echo "No se encontró la línea que contiene 'cookie_key'.";
        }
    }
    

    private function testConnection(){
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
