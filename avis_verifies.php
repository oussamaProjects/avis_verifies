<?php
/**
* 2007-2021 PrestaShop
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
*  @copyright 2007-2021 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_.'avis_verifies/AvisVerifiesModel.php';

class Avis_verifies extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'avis_verifies';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Lnkboot';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Avis vérifiés');
        $this->description = $this->l('Afficher les avis vérifiés sur le home page est les pages catégories');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('AVIS_VERIFIES_ACTIVE', false);
        Configuration::updateValue('AVIS_DISPLAYSNIPPETSITEGLOBAL', 1);

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('displayCategoryBottom') &&
            $this->registerHook('displayHome') &&
            $this->registerHook('CategorysummaryAvisverifies');
    }

    public function uninstall()
    {
        Configuration::deleteByName('AVIS_VERIFIES_ACTIVE');
        Configuration::deleteByName('AVIS_DISPLAYSNIPPETSITEGLOBAL');
        Configuration::deleteByName('AVIS_VERIFIES_HIDE');
        Configuration::deleteByName('AVIS_VERIFIES_URL');

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitAvis_verifiesModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output.$this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitAvis_verifiesModule';
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

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Activé'),
                        'name' => 'AVIS_VERIFIES_ACTIVE',
                        'is_bool' => true,
                        'desc' => $this->l('Use this module'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Afficher en JSON'),
                        'name' => 'AVIS_DISPLAYSNIPPETSITEGLOBAL',
                        'is_bool' => true,
                        'desc' => $this->l('Use this module'),
                        'values' => array(
                            array(
                                'id' => 'json_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'json_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ), array(
                        'type' => 'switch',
                        'label' => $this->l('Caché'),
                        'name' => 'AVIS_VERIFIES_HIDE',
                        'is_bool' => true,
                        'desc' => $this->l('Hide this module'),
                        'values' => array(
                            array(
                                'id' => 'hide_on',
                                'value' => true,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'hide_off',
                                'value' => false,
                                'label' => $this->l('No')
                            )
                        ),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text', 
                        'desc' => $this->l('Enter an url'),
                        'name' => 'AVIS_VERIFIES_URL',
                        'label' => $this->l('URL'),
                    ), 
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'AVIS_VERIFIES_ACTIVE' => Configuration::get('AVIS_VERIFIES_ACTIVE', true),
            'AVIS_DISPLAYSNIPPETSITEGLOBAL' => Configuration::get('AVIS_DISPLAYSNIPPETSITEGLOBAL', true),
            'AVIS_VERIFIES_HIDE' => Configuration::get('AVIS_VERIFIES_HIDE', false),
            'AVIS_VERIFIES_URL' => Configuration::get('AVIS_VERIFIES_URL', 'https://cl.avis-verifies.com/fr/cache/d/9/1/d919589c-e65f-4062-85a9-d0c422586597/AWS/d919589c-e65f-4062-85a9-d0c422586597_infosite.txt'), 
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) { 
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
    }

    public function hookDisplayHome()
    {
        return $this->displayAvis();
    }
   
    /**
     * allow to display a overview of a category,
     * {hook h='displayCategoryBottom'} to be added in product-list.tpl
     */
    public function hookDisplayCategoryBottom()
    {
        return $this->displayCategoryAvis();
    }


    public function displayAvis()
    {

        if($this->isActivated()){

            $data = $this->doCurl(Configuration::get('AVIS_VERIFIES_URL'), 'GET');
            $hide_avis = Configuration::get('AVIS_VERIFIES_HIDE');
            $avis = explode(';', $data); 
 
            $shop_id = (Tools::getValue('id_shop') && !$all_multishops_reviews) ? (int) Tools::getValue('id_shop') : "";
            $avies_display_choice = Configuration::get('AVIS_DISPLAYSNIPPETSITEGLOBAL', null, null, $shop_id);
            $this->context->smarty->assign(
                array( 
                    'avis' => $avis,
                    'hide_avis' => $hide_avis,
                    'avies_display_choice' => !empty($avies_display_choice) ? $avies_display_choice : false,
                )
            );
                
            return $this->display(__FILE__, 'home_avis.tpl');
        }
    }

    /**
     * allow to display a overview of a category,
     * {hook h='displayCategoryBottom'} to be added in product-list.tpl
     */
    public function displayCategoryAvis()
    { 
        
        if($this->isActivated()){
            $all_multishops_reviews = false;
            $shop_id = (Tools::getValue('id_shop') && !$all_multishops_reviews) ? (int) Tools::getValue('id_shop') : "";
            $lang_id = (isset($this->context->language->id) && !empty($this->context->language->id))?(int)$this->context->language->id:1;
    
            // $lang_id = (empty($this->id_lang))?1:$this->id_lang;
            $current_page_name = $this->context->controller->php_self;
            if (($current_page_name == 'manufacturer' || $current_page_name == 'category' )) {
                // find the list of the id in a manufacturer
                if ($current_page_name == 'manufacturer') {
                    $id_manufacturer = (int)Tools::getValue('id_manufacturer');
                    $manu = new Manufacturer($id_manufacturer, $this->context->language->id);
                    $name_subject = $manu->name;
                    $description_subject = $manu->description;

                    $sql = 'SELECT id_product FROM '._DB_PREFIX_.'product where id_manufacturer="'.$id_manufacturer.'"';
                } elseif ($current_page_name == 'category') {
                    $id_category = (int)Tools::getValue('id_category');
                    $cat = new Category($id_category, $this->context->language->id);
                    $url_page = AvisVerifiesModel::getUrlCategory($id_category, $lang_id);
                    $url_image = AvisVerifiesModel::getUrlImageCategory($id_category, null, $lang_id);
                    
                    $name_category = $cat->name;
                    $name_subject = $cat->meta_title;
                    $description_subject = $cat->meta_description;

                    $sql = 'SELECT * FROM '._DB_PREFIX_.'category_product where id_category="'.$id_category.'"';
                }
                        
                $results = Db::getInstance()->ExecuteS($sql);

                // predefine the stats of the reviews, contains the number and the total of the rates
                $stats_product = array('nb_reviews'=>0,'somme'=>0,'nb_discounted'=>0);
                $price_sum = 0;
                $products_info = array();
                        
                $low_price = 0;
                $high_price = 0;
                
                foreach ($results as $row) {

                    $id_product = (int)$row['id_product'];

                    // $products_info[$id_product]['name'] = $this->getProductName($id_product, $lang_id);
                    $products_info[$id_product]['price'] = round(Product::getPriceStatic($id_product), 2);
                    if( Product::isDiscounted($id_product) == true){
                        $stats_product['nb_discounted']++; 
                    }


                    $price_sum += $products_info[$id_product]['price'];

                    $low_price  = ($low_price  < round(Product::getPriceStatic($id_product), 2) && $low_price != 0) ? $low_price  : round(Product::getPriceStatic($id_product), 2);
                    $high_price = ($high_price > round(Product::getPriceStatic($id_product), 2)) ? $high_price : round(Product::getPriceStatic($id_product), 2);

                    $o_av = new AvisVerifiesModel();
                    $reviews = $o_av->getProductReviews($id_product, null, $shop_id, 40);

                    if($_SERVER['REMOTE_ADDR'] == '196.70.254.137'){}
    

                    foreach ($reviews as $review) {
                        // calculate the number of review and the total of the rates
                        $stats_product['nb_reviews']++;
                        $stats_product['somme'] = $stats_product['somme'] + $review['rate'];
                    }
                }

                if(isset($review)){

                    $num_products = (count($results) > 0) ? count($results) : 1;
                    $price_average = $price_sum / $num_products;
                    $average_rate_percent = array();
                    $average_rate_percent['floor'] = floor($review['rate']) - 1;
                    $average_rate_percent['decimals'] = ($review['rate'] - floor($review['rate']))*20;
                    $use_star_format_image = Configuration::get('AV_FORMAT_IMAGE', null, null, $shop_id);

                    if (version_compare(_PS_VERSION_, '1.4', '>=') && $use_star_format_image != '1') {
                        $stars_file = 'avisverifies-stars-font.tpl';
                    } else {
                        $stars_file = 'avisverifies-stars-image.tpl';
                        $use_image = true;
                    }

                    $avies_display_choice = Configuration::get('AVIS_DISPLAYSNIPPETSITEGLOBAL', null, null, $shop_id);
                    $brand = Configuration::get('PS_SHOP_NAME');

                    // calcul de la moyen
                    if ($stats_product['nb_reviews'] > 0) {
                        $stats_product['rate'] = $stats_product['somme'] / $stats_product['nb_reviews'];

                        $values = array(

                            'category' => !empty($name_category) ? strip_tags($name_category) : false,
                            'category_name' => !empty($name_subject) ? strip_tags($name_subject) : false,
                            'category_description' => !empty($description_subject) ? strip_tags($description_subject) : false,
                            'category_image' => !empty($url_image) ? $url_image : false, 
                            
                            'category_url' =>  !empty($url_page) ? $url_page : false,

                            'brand' =>  $brand,
                            'sku' =>  false,
                            'mpn' =>  false,
                            'gtin_ean' =>  false,
                            'gtin_upc' =>  false,

                            'category_rating_value' => round($stats_product['rate'], 1),
                            'category_rating_count' => $stats_product['nb_reviews'],
                            'category_discounted_count' => $stats_product['nb_discounted'],
                            'category_average_rate_percent' => $average_rate_percent,
                            
                            'price_average' => $price_average,
                            'low_price' => $low_price,
                            'high_price' => $high_price,

                            'products_av'=> $products_info,
                            'modules_dir' => _MODULE_DIR_,
                            'stars_dir' => _PS_ROOT_DIR_.'/modules/avisverifies/views/templates/hook/sub/'.$stars_file, 
                            'avies_display_choice' => !empty($avies_display_choice) ? $avies_display_choice : false,

                        );
                        $this->smartyAssign($values);

                        $tpl = 'avisverifies-category-summary';
                        // return null;
                        return $this->displayTemplate($tpl);
                    }
                }
            }
        }
    }
    protected function smartyAssign($smarty_array)
    {
        if (version_compare(_PS_VERSION_, '1.5', '<')) {
            global  $smarty;
            return  $smarty->assign($smarty_array);
        } elseif (version_compare(_PS_VERSION_, '1.5', '>=')) {
            return  $this->context->smarty->assign($smarty_array);
        }
    }
    protected function displayTemplate($tpl)
    {
        // if (version_compare(_PS_VERSION_, '1.6', '<')) {
        return  ($this->display(__FILE__, "/views/templates/hook/$tpl.tpl"));
        // } else {
        //     return  ($this->display(__FILE__, "/views/templates/hook/$tpl.tpl"));
        // }
    }
    /**
     * Checks if Plugin is enabled.
     *
     * @return string
     */
    public static function isActivated()
    {
        return Configuration::get('AVIS_VERIFIES_ACTIVE');
    }

     /**
     * Makes a curl request.
     *
     * @param string $requestUrl  Api End point url.
     * @param string $requestType Api Request type.
     * @param array  $httpHeader  Header.
     * @param string $postFields  The post data to send.
     * @return mixed|string
     */
    public function doCurl($requestUrl, $requestType, $httpHeader = array(), $postFields = '')
    {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $requestUrl);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $requestType);
            if (!empty($httpHeader)) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeader);
            }

            if (!empty($postFields)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
            }

            $response = curl_exec($ch);
            curl_close($ch);

            return $response;
        } catch (Exception $exception) {
            PrestaShopLogger::addLog($exception->getMessage(), 1);
            return $exception->getMessage();
        }
    }
}
