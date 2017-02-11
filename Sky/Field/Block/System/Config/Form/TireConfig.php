<?php 

namespace Sky\Field\Block\System\Config\Form;

class TireConfig extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{

	protected $_AllCountryList;

	 public function _construct() {
	   parent::_construct();
	  $this->addColumn('customer_group', array(
	    'label'=> __('Customer Group'),
	    'style' => '100px',
	   ));

	   $this->addColumn('price', array(
	    'label'=> __('Price'),
	    'style' => '100px',
	   ));

	 }
 

 public function renderCellTemplate($columnName)
    {
        if (empty($this->_columns[$columnName])) {
            throw new \Exception('Wrong column name specified.');
        }
        $column = $this->_columns[$columnName];
        $inputName = $this->_getCellInputElementName($columnName);

        if ($column['renderer']) {
            return $column['renderer']->setInputName(
                $inputName
            )->setInputId(
                $this->_getCellInputElementId('<%- _id %>', $columnName)
            )->setColumnName(
                $columnName
            )->setColumn(
                $column
            )->toHtml();
        }

      if($columnName == 'customer_group'){
       return $this->_renderCellCustomerGroup($columnName, $inputName, $column);
        }

        return '<input type="text" id="' . $this->_getCellInputElementId(
            '<%- _id %>',
            $columnName
        ) .
            '"' .
            ' name="' .
            $inputName .
            '" value="<%- ' .
            $columnName .
            ' %>" ' .
            ($column['size'] ? 'size="' .
            $column['size'] .
            '"' : '') .
            ' class="' .
            (isset(
            $column['class']
        ) ? $column['class'] : 'input-text') . '"' . (isset(
            $column['style']
        ) ? ' style="' . $column['style'] . '"' : '') . '/>';
    }

	private function _renderCellCustomerGroup($columnName, $inputName, $column){
	
   $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
   $allCountry =$objectManager->create('Magento\Directory\Model\Config\Source\Country')->toOptionArray();

	$optionHtml = '';
	foreach($allCountry as $key => $value){
	  $optionHtml.='<options value="'.$value['value'].'">'.$value['label'].'</option>';
	}

	 return $this->_renderCellSelect($columnName, $inputName, $column, $optionHtml);
	}


	private function _renderCellSelect($columnName, $inputName, $column, $optionHtml){
  // echo 'ab'.$columnName;
  // echo 'df'.$inputName.'<br>';
  // print_r($column);
  // echo '<br>'.$optionHtml;
  //  die;
		//$column['class']= 'select multiselect admin__control-multiselect';
	return $fieldhtmt = '<select id="' . $this->_getCellInputElementId(
            '<%- _id %>',
            $columnName
        ) .
            '"' .
            ' name="' .
            $inputName .
            '[]" value="<%- ' .
            $columnName .
            ' %>" ' .
            ($column['size'] ? 'size="' .
            $column['size'] .
            '"' : '') .
            ' class="' .
            (isset(
            $column['class']
        ) ? $column['class'] : 'input-text') . '"' . (isset(
            $column['style']
        ) ? ' style="' . $column['style'] . '"' : '') . ' data-ui-id="select-groups-country-fields-allow-value"><option value="1">dddd</option><option value="2">surendra</option></select>';


	}


	

} 