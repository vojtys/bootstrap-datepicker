<?php
namespace Vojtys\Forms\DatePicker;

use Nette\Utils;
use Nette\Forms\Form;


/**
 * Class DatePickerInput
 * @package Vojtys\Forms\DatePicker
 */
class DatePickerInput extends DatePickerBase
{

    function getControl()
    {
        // create input element
        $el = parent::getControl();
        $el->addAttributes(array('class' => 'form-control'));

        // converts datetime value into php format
        $el->value = $this->value ? $this->value->format($this->toPhpDateTimeFormat($this->dateTimeFormat)) : NULL;

        // generate field group
        $group = Utils\Html::el('div');
        $group->addAttributes([
            'data-vojtys-forms-datepicker' => '',
            'class' => 'input-group date',
            'data-locale' => $this->getLanguage(),
            'data-settings' => $this->getControlSettings()
        ]);

        // add input and icon into group
        $group->add($el)->add(Utils\Html::el('span')->class('input-group-addon')
            ->add(Utils\Html::el('span')->class('glyphicon glyphicon-calendar'))
        );

        return $group;
    }

    /**
     * @param $value
     * @return $this|\Nette\Forms\Controls\BaseControl
     *
     * @throws DatePickerException
     */
    public function setValue($value)
    {
        $this->value = $this->prepareValue($value);
        return $this;
    }

    /**
     * @return FALSE|mixed|Utils\DateTime|null
     */
    public function getValue()
    {
        $value = parent::getValue();
        if ($value instanceof Utils\DateTime) {
            return $value;
        } else if ($value === FALSE || $value === NULL) {
            return NULL;
        } else {
            $value = Utils\DateTime::createFromFormat($this->getDateTimeFormat(TRUE), $value);
            if ($value === FALSE) {
                return NULL;
            }
        }
        return $value;
    }

    public function loadHttpData()
    {
        $this->setValue(Utils\DateTime::createFromFormat($this->getDateTimeFormat(TRUE), $this->getHttpData(Form::DATA_LINE)));
    }
}