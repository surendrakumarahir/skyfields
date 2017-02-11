<?php

namespace Excellence\GeoIp\Block\Adminhtml\System\Config\Form;

use Magento\Store\Model\ScopeInterface;

class CurrencyMapping extends \Magento\Config\Block\System\Config\Form\Field
{
    protected $_moduleList;
    protected $_moduleManager;
    protected $_productMetadata;
    protected $_serverAddress;
    protected $_storeManager;
    protected $_cacheManager;
    protected $_objectManager;

    public function __construct(
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Framework\HTTP\PhpEnvironment\ServerAddress $serverAddress,
        \Magento\Framework\App\Cache\Proxy $cacheManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigObject,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->_moduleList       = $moduleList;
        $this->_moduleManager    = $moduleManager;
        $this->_storeManager     = $storeManager;
        $this->_productMetadata  = $productMetadata;
        $this->_serverAddress    = $serverAddress;
        $this->_cacheManager    = $cacheManager;
        $this->_objectManager    = $objectManager;
        $this->_request = $request;
        $this->_scopeConfigObject = $scopeConfigObject;
    }

    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return $this->getModuleInfoHtml();
    }

    public function getWikiLink()
    {
        return $this->_wikiLink;
    }

    public function getModuleTitle()
    {
        return $this->_moduleName;
    }

    public function getModuleInfoHtml()
    {
        $optionArray = array();
        $html = '';
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $countryModel = $objectManager->get('Magento\Directory\Model\Country');
        $currencyModel = $objectManager->get('Magento\Directory\Model\CurrencyFactory')->create()->load('INR');
        print_r($currencyModel->getName()); die();

        $mappingJson = $this->_scopeConfigObject->getValue(
                    'geoip/currency_mapping/selected_mapping',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $this->_request->getParam('store')
                    );
        $mappedData = json_decode($mappingJson, true);

        foreach ($mappedData as $countryCode => $currencyCode) {
            $html .= "<tr>
                          <td>".$countryModel->loadByCode($countryCode)->getName()."</td>
                          <td>".$subHtml."</td>
                        </tr>";
        }
        // print_r($selected); die();
        echo "<pre>";
        print_r(json_decode($mappingJson, true)); die();

        $allStores = $this->_storeManager->getStores();
        $savedCountries = array();
        foreach ($allStores as $_eachStoreId => $val) 
        {
            $_storeCode = $this->_storeManager->getStore($_eachStoreId)->getCode();
            $_storeName = $this->_storeManager->getStore($_eachStoreId)->getName();
            $_storeId = $this->_storeManager->getStore($_eachStoreId)->getId();
            $selected = $this->_scopeConfigObject->getValue(
                    'geoip/currency_mapping/selected_mapping',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $_storeId
                    );
            $savedCountries = explode(',', $selected);
            $selectedStoreCountry = array();
            foreach ($optionArray as $key=>$option) {
            if (in_array($option['value'], $savedCountries)){
                    $selectedStoreCountry[] = $option['label'];
                }
            }
            if(count($selectedStoreCountry) > 0){
                $subHtml = implode(', ', $selectedStoreCountry);

                $html .= "<tr>
                              <td>".$_storeName."</td>
                              <td>".$subHtml."</td>
                            </tr>";
            }
                
        }
        if(strlen($html) == 0){
            return;
        }

        $tableHtml = "<table>
                      <thead>
                        <tr>
                          <th>".__('Selected Country')."</th>
                          <th>".__('Mapped Currency')."</th>
                        </tr>
                      </thead>
                      <tbody>"
                      .$html.
                      "</tbody>
                    </table>";
        
        $m = $this->_moduleList->getOne($this->getModuleName());
        if(!empty($this->_request->getParam('store'))){
            $finalHtml = '<tr><td class="label" colspan="4" style="text-align: left;"><div style="padding:10px;background-color:#f8f8f8;border:1px solid #ddd;margin-bottom:7px;">
            <h2>'.__('Previously Selected Countries:').'</h2>
            ' 
            .$tableHtml.
            '<p style="margin: 20px 0 5px 0;color: #FF0000;"><span style="margin-right: 5px;">&#10148;</span>'.__('Please select the countries which are not selected in other store views').'</p>'.
            '</div></td></tr>';
        }
        else{
            $finalHtml = '<tr><td class="label" colspan="4" style="text-align: left;"><div style="padding:10px;background-color:#f8f8f8;border:1px solid #ddd;margin-bottom:7px;">
            <h2>'.__('Previously Selected Countries:').'</h2>
            ' 
            .$tableHtml.
            '</div></td></tr>';
        }

        return $finalHtml;
    }

}