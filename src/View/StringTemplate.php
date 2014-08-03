<?php
namespace Bootstrap\View;

use Cake\View;

class StringTemplate extends View\StringTemplate
{
    public function format($name, array $data)
    {
        switch ($name) {
            case 'checkboxFormGroup':
                $data['input'] = preg_replace('/(<label(?:[^>]+)>)([^<]+)(<\/label>)/i', "$1${data['input']} $2$3", $data['label']);
                unset($data['label']);
            break;
        }
        return parent::format($name, $data);
    }
}
