<?php $tableId = 0; ?>
<?php foreach ($table->getTables() as $tbl): ?>
    <table data-table-id="<?php echo $tableId; ?>" class="table table-result" width="100%">
        <tbody>
            <tr class="titles">
                <?php foreach ($tbl['columns'] as $column): ?>
                    <th data-width="<?php echo $column['width']; ?>" width="<?php echo round((float) $column['width'] * 100); ?>%">
                        <?php echo CHtml::encode($column['name']); ?>
                    </th>
                <?php endforeach; ?>
            </tr>
            <?php $entryId = 0; ?>
            <?php foreach ($tbl['data'] as $row): ?>
                <tr class="data" data-id="<?php echo $entryId; ?>">
                    <?php foreach ($row as $cell): ?>
                        <td>
                            <?php echo $cell; ?>
                        </td>
                    <?php endforeach; ?>
                    <td class="actions">
                        <a href="#del" title="<?php echo Yii::t('app', 'Delete'); ?>" onclick="user.gtCheck.delTableResultEntry('<?php echo $tableId; ?>', '<?php echo $entryId; ?>');"><i class="icon icon-remove"></i></a>
                    </td>
                </tr>
                <?php $entryId++ ?>
            <?php endforeach; ?>
            <tr class="new-entry">
                <?php for ($i = 0; $i < count($tbl['columns']); $i++): ?>
                    <td>
                        <input type="text" />
                    </td>
                <?php endfor; ?>
                <td class="actions">
                    <a href="#add" title="<?php echo Yii::t('app', 'Add'); ?>" onclick="user.gtCheck.newTableResultEntry('<?php echo $tableId; ?>');"><i class="icon icon-ok"></i></a>
                </td>
            </tr>
        </tbody>
        <?php $tableId++; ?>
    </table>
<?php endforeach; ?>