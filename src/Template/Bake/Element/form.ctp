<%
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * Slightly modified by Òscar Casajuana for the twbs-cake-plugin
 * also under the MIT license.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         0.1.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
use Cake\Utility\Inflector;

$fields = collection($fields)
    ->filter(function($field) use ($schema) {
        return $schema->columnType($field) !== 'binary';
    });
$pk = "\${$singularVar}->{$primaryKey[0]}";
%>
<div class="actions columns col-lg-2 col-md-3">
    <h3><?= __('Actions') ?></h3>
    <ul class="nav nav-stacked nav-pills">
<% if (strpos($action, 'add') === false): %>
        <li class="active disabled"><?= $this->Html->link(__('Edit <%= $singularHumanName %>'), ['action' => 'edit', <%= $pk %>]) ?> </li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', <%= $pk %>],
                ['confirm' => __('Are you sure you want to delete # {0}?', $<%= $singularVar %>-><%= $primaryKey[0] %>), 'class' => 'btn-danger']
            )
        ?></li>
        <li><?= $this->Html->link(__('New <%= $singularHumanName %>'), ['action' => 'add']) ?></li>
<% else: %>
        <li class="active disabled"><?= $this->Html->link(__('New <%= $singularHumanName %>'), ['action' => 'add']) ?></li>
<% endif; %>
        <li><?= $this->Html->link(__('List <%= $pluralHumanName %>'), ['action' => 'index']) ?></li>
<%
        $done = [];
        foreach ($associations as $type => $data) {
            foreach ($data as $alias => $details) {
                if ($details['controller'] != $this->name && !in_array($details['controller'], $done)) {
%>
        <li><?= $this->Html->link(__('List <%= $this->_pluralHumanName($alias) %>'), ['controller' => '<%= $details['controller'] %>', 'action' => 'index']) %> </li>
        <li><?= $this->Html->link(__('New <%= $this->_singularHumanName($alias) %>'), ['controller' => '<%= $details['controller'] %>', 'action' => 'add']) %> </li>
<%
                    $done[] = $details['controller'];
                }
            }
        }
%>
    </ul>
</div>
<div class="<%= $pluralVar %> form col-lg-10 col-md-9 columns">
    <?= $this->Form->create($<%= $singularVar %>); ?>
    <fieldset>
        <legend><?= __('<%= Inflector::humanize($action) %> <%= $singularHumanName %>') ?></legend>
        <?php
<%
        foreach ($fields as $field) {
            if (in_array($field, $primaryKey)) {
                continue;
            }
            if (isset($keyFields[$field])) {
                $fieldData = $schema->column($field);
                if (!empty($fieldData['null'])) {
%>
            echo $this->Form->input('<%= $field %>', ['options' => $<%= $keyFields[$field] %>, 'empty' => true]);
<%
                } else {
%>
            echo $this->Form->input('<%= $field %>', ['options' => $<%= $keyFields[$field] %>]);
<%
                }
                continue;
            }
            if (!in_array($field, ['created', 'modified', 'updated'])) {
%>
            echo $this->Form->input('<%= $field %>');
<%
            }
        }
        if (!empty($associations['BelongsToMany'])) {
            foreach ($associations['BelongsToMany'] as $assocName => $assocData) {
%>
            echo $this->Form->input('<%= $assocData['property'] %>._ids', ['options' => $<%= $assocData['variable'] %>]);
<%
            }
        }
%>
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit'), ['class' => 'btn-success']) ?>
    <?= $this->Form->end() ?>
</div>
