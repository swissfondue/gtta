<?php if (User::checkRole(User::ROLE_USER)): ?>
    <div class="active-header">
        <div class="pull-right">
            <ul class="nav nav-pills">
                <li><a href="<?php echo $this->createUrl('project/target', array( 'id' => $project->id, 'target' => $target->id )); ?>"><?php echo Yii::t('app', 'View'); ?></a></li>
                <li><a href="<?php echo $this->createUrl('project/edittarget', array( 'id' => $project->id, 'target' => $target->id )); ?>"><?php echo Yii::t('app', 'Edit'); ?></a></li>
                <li class="active"><a href="<?php echo $this->createUrl('project/editchain', array( 'id' => $project->id, 'target' => $target->id )); ?>"><?php echo Yii::t('app', 'Check Chain'); ?></a></li>
            </ul>
        </div>
        <div class="pull-right buttons">
            <?php if (User::checkRole(User::ROLE_USER)): ?>
                <button
                   title="Reset"
                   class="btn chain-reset-button"
                   data-target-id="<?php print $target->id; ?>"
                   data-control-url="<?php echo $this->createUrl('project/controlchain', array( 'id' => $project->id, 'target' => $target->id )); ?>"
                   onclick="user.target.chain.reset($(this).data('target-id'), $(this).data('control-url'))"
                   <?php if ($target->isChainRunning) print 'disabled'; ?>><i class="icon icon-refresh"></i></button>&nbsp;
                <button href="#startchain"
                   title="Start"
                   class="btn chain-start-button <?php if ($target->isChainRunning) print 'hide'; ?>"
                   data-target-id="<?php print $target->id; ?>"
                   data-control-url="<?php echo $this->createUrl('project/controlchain', array( 'id' => $project->id, 'target' => $target->id )); ?>"
                   onclick="user.target.chain.start($(this).data('target-id'), $(this).data('control-url'))"><i class="icon icon-play"></i><?php echo Yii::t("app", "Start"); ?></button>&nbsp;
                <button href="#stopchain"
                   title="Stop"
                   class="btn chain-stop-button <?php if (!$target->isChainRunning) print 'hide'; ?>"
                   data-target-id="<?php print $target->id; ?>"
                   data-control-url="<?php echo $this->createUrl('project/controlchain', array( 'id' => $project->id, 'target' => $target->id )); ?>"
                   onclick="user.target.chain.stop($(this).data('target-id'), $(this).data('control-url'))"
                   <?php if ($target->isChainRunning) print 'disabled'; ?>><i class="icon icon-stop"></i><?php echo Yii::t("app", "Stop"); ?></button>
            <?php endif; ?>
        </div>

        <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
    </div>
<?php else: ?>
    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
<?php endif; ?>

<hr>

<form id="object-selection-form" class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post" data-object-list-url="<?php print $this->createUrl("app/objectlist"); ?>">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">

    <fieldset>
        <div class="control-group relations-graph <?php if ($model->getError('relations')) echo 'error'; ?>" data-get-check-url="<?php print $this->createUrl('project/checklink'); ?>" data-target-id="<?= $target->id ?>">
            <label class="control-label"><?php echo Yii::t('app', 'Relations'); ?></label>
            <div class="controls">
                <table border="0" width="730px">
                    <tr>
                        <td valign="top">
                            <div id="graph"></div>
                        </td>
                        <td id="toolbar" valign="top"></td>
                    </tr>
                </table>
                <?php if ($model->getError('relations')): ?>
                    <p class="help-block"><?php echo $model->getError('relations'); ?></p>
                <?php endif; ?>
                <div id="zoomActions"></div>
                <div id="activeChainCheck" data-url="<?php print $this->createUrl("project/chainactivecheck", array("id" => $project->id, "target" => $target->id)); ?>" class="<?php if (!$activeCheck) print 'hide'; ?>">
                    <?php print Yii::t("app", "Current Check") . ": ";?> <strong class="check-name"><?php print $activeCheck; ?></strong>
                </div>
            </div>
        </div>

        <input type="hidden" class="relations-form-input" id="TargetChainEditForm_relations" name="TargetChainEditForm[relations]" />

        <div class="form-actions">
            <button type="submit" class="btn"><?php echo Yii::t('app', 'Save'); ?></button>
        </div>
    </fieldset>
</form>
<script>
    function onInit(editor) {
        admin.mxgraph.init.call(this, editor);
    }

    var configNode = mxUtils.load("<?php echo Yii::app()->request->baseUrl; ?>/js/mxgraph/grapheditor/config/main.xml").getDocumentElement();
    admin.mxgraph.editor = new mxEditor(configNode);

    <?php foreach ($categories as $category): ?>
        admin.mxgraph.checkCategories.push({
            id : <?php print $category->id; ?>,
            name : "<?php print $category->localizedName; ?>"
        });
    <?php endforeach; ?>

    <?php foreach ($filters as $filter): ?>
        admin.mxgraph.filters.push({ name: "<?php print $filter['name']; ?>", title: "<?php print $filter['title']; ?>" });
    <?php endforeach; ?>

    $('#languages-tab a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    });

    user.target.targetId = parseInt("<?php print $target->id; ?>");
    admin.mxgraph.buildByXML('<?php print $model->relations; ?>');

    setInterval(function () {
        user.target.chain.messages('<?php print $this->createUrl('project/chainmessages'); ?>');
    }, 5000);

    <?php if ($target->isChainRunning): ?>
        setTimeout(function () {
            user.target.chain.updateActiveCheck("<?php print $this->createUrl("project/chainactivecheck", array("id" => $project->id, "target" => $target->id)); ?>");
        }, 5000);
    <?php endif; ?>

    admin.mxgraph.target = parseInt('<?php print $target->id; ?>')
</script>