<table class="table table-result" width="100%">
    <tbody>
        <tr>
            <?php foreach ($table->columns as $column): ?>
                <th width="<?php echo round((float)$column['width'] * 100); ?>%">
                    <?php echo CHtml::encode($column['name']); ?>
                </th>
            <?php endforeach; ?>
        </tr>
        <?php foreach ($table->data as $row): ?>
            <tr>
                <?php foreach ($row as $cell): ?>
                    <td>
                        <?php echo $cell; ?>
                    </td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>