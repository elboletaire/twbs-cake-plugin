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

$associations += ['BelongsTo' => [], 'HasOne' => [], 'HasMany' => [], 'BelongsToMany' => []];
$immediateAssociations = $associations['BelongsTo'] + $associations['HasOne'];
$associationFields = collection($fields)
    ->map(function($field) use ($immediateAssociations) {
        foreach ($immediateAssociations as $alias => $details) {
            if ($field === $details['foreignKey']) {
                return [$field => $details];
            }
        }
    })
    ->filter()
    ->reduce(function($fields, $value) {
        return $fields + $value;
    }, []);

$groupedFields = collection($fields)
    ->filter(function($field) use ($schema) {
        return $schema->columnType($field) !== 'binary';
    })
    ->groupBy(function($field) use ($schema, $associationFields) {
        $type = $schema->columnType($field);
        if (isset($associationFields[$field])) {
            return 'string';
        }
        if (in_array($type, ['integer', 'float', 'decimal', 'biginteger'])) {
            return 'number';
        }
        if (in_array($type, ['date', 'time', 'datetime', 'timestamp'])) {
            return 'date';
        }
        return in_array($type, ['text', 'boolean']) ? $type : 'string';
    })
    ->toArray();

$groupedFields += ['number' => [], 'string' => [], 'boolean' => [], 'date' => [], 'text' => []];
$pk = "\$$singularVar->{$primaryKey[0]}";
%>
<div class="actions columns col-lg-2 col-md-3">
    <h3><?= __('Actions') ?></h3>
    <ul class="nav nav-stacked nav-pills">
        <li><?= $this->Html->link(__('Edit <%= $singularHumanName %>'), ['action' => 'edit', <%= $pk %>]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete <%= $singularHumanName %>'), ['action' => 'delete', <%= $pk %>], ['confirm' => __('Are you sure you want to delete # {0}?', <%= $pk %>), 'class' => 'btn-danger']) ?> </li>
        <li><?= $this->Html->link(__('List <%= $pluralHumanName %>'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New <%= $singularHumanName %>'), ['action' => 'add']) ?> </li>
<%
    $done = [];
    foreach ($associations as $type => $data) {
        foreach ($data as $alias => $details) {
            if ($details['controller'] != $this->name && !in_array($details['controller'], $done)) {
%>
        <li><?= $this->Html->link(__('List <%= $this->_pluralHumanName($alias) %>'), ['controller' => '<%= $details['controller'] %>', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New <%= Inflector::humanize(Inflector::singularize(Inflector::underscore($alias))) %>'), ['controller' => '<%= $details['controller'] %>', 'action' => 'add']) ?> </li>
<%
                $done[] = $details['controller'];
            }
        }
    }
%>
    </ul>
</div>
<div class="<%= $pluralVar %> view col-lg-10 col-md-9 columns">
    <h2><?= h($<%= $singularVar %>-><%= $displayField %>) ?></h2>
    <div class="row">
<% if ($groupedFields['string']) : %>
        <div class="col-lg-5 columns strings">
            <div class="panel panel-default">
                <div class="panel-body">
<% foreach ($groupedFields['string'] as $field) : %>
<% if (isset($associationFields[$field])) :
            $details = $associationFields[$field];
%>
                    <h6 class="subheader"><?= __('<%= Inflector::humanize($details['property']) %>') ?></h6>
                    <p><?= $<%= $singularVar %>->has('<%= $details['property'] %>') ? $this->Html->link($<%= $singularVar %>-><%= $details['property'] %>-><%= $details['displayField'] %>, ['controller' => '<%= $details['controller'] %>', 'action' => 'view', $<%= $singularVar %>-><%= $details['property'] %>-><%= $details['primaryKey'][0] %>]) : '' ?></p>
<% else : %>
                    <h6 class="subheader"><?= __('<%= Inflector::humanize($field) %>') ?></h6>
                    <p><?= h($<%= $singularVar %>-><%= $field %>) ?></p>
<% endif; %>
<% endforeach; %>
                </div>
            </div>
        </div>
<% endif; %>
<% if ($groupedFields['number']) : %>
        <div class="col-lg-2 columns numbers end">
            <div class="panel panel-default">
                <div class="panel-body">
<% foreach ($groupedFields['number'] as $field) : %>
                    <h6 class="subheader"><?= __('<%= Inflector::humanize($field) %>') ?></h6>
                    <p><?= $this->Number->format($<%= $singularVar %>-><%= $field %>) ?></p>
<% endforeach; %>
                </div>
            </div>
        </div>
<% endif; %>
<% if ($groupedFields['date']) : %>
        <div class="col-lg-2 columns dates end">
            <div class="panel panel-default">
                <div class="panel-body">
<% foreach ($groupedFields['date'] as $field) : %>
                    <h6 class="subheader"><%= "<%= __('" . Inflector::humanize($field) . "') %>" %></h6>
                    <p><?= h($<%= $singularVar %>-><%= $field %>) ?></p>
<% endforeach; %>
                </div>
            </div>
        </div>
<% endif; %>
<% if ($groupedFields['boolean']) : %>
        <div class="col-lg-2 columns booleans end">
            <div class="panel panel-default">
                <div class="panel-body">
<% foreach ($groupedFields['boolean'] as $field) : %>
                    <h6 class="subheader"><?= __('<%= Inflector::humanize($field) %>') ?></h6>
                    <p><?= $<%= $singularVar %>-><%= $field %> ? __('Yes') : __('No'); ?></p>
<% endforeach; %>
                </div>
            </div>
        </div>
<% endif; %>
    </div>
<% if ($groupedFields['text']) : %>
<% foreach ($groupedFields['text'] as $field) : %>
    <div class="row texts">
        <div class="columns col-lg-9">
            <div class="panel panel-default">
                <div class="panel-body">
                    <h6 class="subheader"><?= __('<%= Inflector::humanize($field) %>') ?></h6>
                    <?= $this->Text->autoParagraph(h($<%= $singularVar %>-><%= $field %>)); ?>
                </div>
            </div>
        </div>
    </div>
<% endforeach; %>
<% endif; %>
</div>
<%
$relations = $associations['HasMany'] + $associations['BelongsToMany'];
foreach ($relations as $alias => $details):
    $otherSingularVar = Inflector::variable($alias);
    $otherPluralHumanName = Inflector::humanize($details['controller']);
    %>
<div class="related row">
    <div class="column col-lg-12">
    <h4 class="subheader"><?= __('Related <%= $otherPluralHumanName %>') ?></h4>
    <?php if (!empty($<%= $singularVar %>-><%= $details['property'] %>)): ?>
    <div class="table-responsive">
        <table class="table">
            <tr>
    <% foreach ($details['fields'] as $field): %>
            <th><?= __('<%= Inflector::humanize($field) %>') ?></th>
    <% endforeach; %>
            <th class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($<%= $singularVar %>-><%= $details['property'] %> as $<%= $otherSingularVar %>): ?>
            <tr>
                <%- foreach ($details['fields'] as $field): %>
                <td><?= h($<%= $otherSingularVar %>-><%= $field %>) ?></td>
                <%- endforeach; %>
                <%- $otherPk = "\${$otherSingularVar}->{$details['primaryKey'][0]}"; %>
                <td class="actions">
                    <?= $this->Html->link('<span class="glyphicon glyphicon-zoom-in"></span><span class="sr-only">' . __('View') . '</span>', ['controller' => '<%= $details['controller'] %>', 'action' => 'view', <%= $otherPk %>], ['escape' => false, 'class' => 'btn btn-xs btn-default', 'title' => __('View')]) ?>
                    <?= $this->Html->link('<span class="glyphicon glyphicon-pencil"></span><span class="sr-only">' . __('Edit') . '</span>', ['controller' => '<%= $details['controller'] %>', 'action' => 'edit', <%= $otherPk %>], ['escape' => false, 'class' => 'btn btn-xs btn-default', 'title' => __('Edit')]) ?>
                    <?= $this->Form->postLink('<span class="glyphicon glyphicon-trash"></span><span class="sr-only">' . __('Delete') . '</span>', ['controller' => '<%= $details['controller'] %>', 'action' => 'delete', <%= $otherPk %>], ['confirm' => __('Are you sure you want to delete # {0}?', <%= $otherPk %>), 'escape' => false, 'class' => 'btn btn-xs btn-danger', 'title' => __('Delete')]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <?php endif; ?>
    </div>
</div>
<% endforeach; %>
