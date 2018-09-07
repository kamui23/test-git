<?php

namespace Kemana\Core\Block\Magestore\Adminhtml\Store\Edit\Tab;

class GmapTab extends \Magestore\Storepickup\Block\Adminhtml\Store\Edit\Tab\GmapTab
{
    protected function _prepareForm()
    {
        $model = $this->getRegistryModel();
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('store_');
        $fieldset = $form->addFieldset('gmap_fieldset', ['legend' => __('Location Information')]);
        if ($model->getId()) {
            $fieldset->addField('storepickup_id', 'hidden', ['name' => 'storepickup_id']);
        }

        $fieldset->addField(
            'address',
            'text',
            [
                'name'        => 'address',
                'label'       => __('Address'),
                'title'       => __('Address'),
                'required'    => true,
                'placeholder' => 'Enter your address',
            ]
        );

        $fieldset->addField(
            'city',
            'text',
            [
                'name'        => 'city',
                'label'       => __('City'),
                'title'       => __('City'),
                'placeholder' => 'City',
            ]
        );

        $fieldset->addField(
            'zipcode',
            'text',
            [
                'name'        => 'zipcode',
                'label'       => __('Zip Code'),
                'title'       => __('Zip Code'),
                'placeholder' => 'Zip Code',
            ]
        );

        $fieldset->addField(
            'country_id',
            'select',
            [
                'label'  => __('Country'),
                'title'  => __('Country'),
                'name'   => 'country_id',
                'values' => $this->_localCountry->toOptionArray(),
                'style'  => 'width: 100%;',
            ]
        );

        $fieldset->addField(
            'region_updater',
            'note',
            [
                'name'  => 'region_updater',
                'label' => __('State/Province'),
                'title' => __('State/Province'),
                'text'  => $this->getChildHtml('store_edit_region_updater'),
                'style' => 'width:100%;',
            ]
        );

        $fieldset->addField(
            'latitude',
            'text',
            [
                'name'     => 'latitude',
                'label'    => __('Latitude'),
                'title'    => __('Latitude'),
                'required' => true,
            ]
        );

        $fieldset->addField(
            'longitude',
            'text',
            [
                'name'     => 'longitude',
                'label'    => __('Longitude'),
                'title'    => __('Longitude'),
                'required' => true,
            ]
        );

        $fieldset->addField(
            'zoom_level',
            'text',
            [
                'name'     => 'zoom_level',
                'label'    => __('Zoom Level'),
                'title'    => __('Zoom Level'),
                'required' => true,
            ]
        );

        $fieldset->addField(
            'marker_icon',
            'image',
            [
                'name'  => 'marker_icon',
                'label' => __('Marker Icon'),
                'title' => __('Marker Icon'),
                'note'  => __('Recommended size: 400x600 px. Supported format: jpg, jpeg, gif, png.'),
            ]
        );

        $mapBlock = $this->getLayout()
                         ->createBlock('Magestore\Storepickup\Block\Adminhtml\Store\Edit\Tab\GmapTab\Renderer\Map');

        $fieldset->addField(
            'googlemap',
            'text',
            [
                'label' => __('Store Map'),
                'name'  => 'googlemap',
            ]
        )->setRenderer($mapBlock);

        if (!$model->getId()) {
            $model->setLatitude('0.00000000')
                  ->setLongitude('0.00000000')
                  ->setZoomLevel(4);
        }

        if (is_array($model->getData('marker_icon'))) {
            $model->setData('marker_icon', $model->getData('marker_icon/value'));
        }

        $form->setValues($model->getData());
        $this->setForm($form);

        return $this;
    }

}