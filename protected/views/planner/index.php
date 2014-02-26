<script src="/js/planner.js"></script>

<div class="active-header">
    <div class="pull-right buttons">
        <a class="btn" href="#add" onclick="admin.planner.planForm();"><i class="icon icon-plus"></i> <?php echo Yii::t("app", "Add Plan"); ?></a>
    </div>

    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<div class="container">
    <div class="row">
        <div class="span12">
            <div class="planner"></div>
            <div class="clearfix"></div>
            &nbsp;
        </div>
    </div>
</div>

<div class="modal fade hide" id="plan-modal">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h3><?php echo Yii::t("app", "Add Plan"); ?></h3>
    </div>
    <div class="modal-body">
        <div class="modal-text">
            <?php echo Yii::t("app", "Please select a user and project category or module."); ?>
        </div>

        <form id="object-selection-form" class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post" data-object-list-url="<?php echo $this->createUrl("app/objectlist"); ?>">
            <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">

            <fieldset>
                <div class="control-group">
                    <label class="control-label" for="ProjectPlannerEditForm_startDate"><?php echo Yii::t("app", "Start"); ?></label>
                    <div class="controls">
                        <input type="text" class="input-xlarge datepicker" id="ProjectPlannerEditForm_startDate" name="ProjectPlannerEditForm[startDate]" value="<?php echo date("Y-m-d"); ?>" readonly data-date="<?php echo date("Y-m-d"); ?>" data-date-format="yyyy-mm-dd">
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="ProjectPlannerEditForm_endDate"><?php echo Yii::t("app", "End"); ?></label>
                    <div class="controls">
                        <input type="text" class="input-xlarge datepicker" id="ProjectPlannerEditForm_endDate" name="ProjectPlannerEditForm[endDate]" value="<?php echo date("Y-m-d"); ?>" readonly data-date="<?php echo date("Y-m-d"); ?>" data-date-format="yyyy-mm-dd">
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="ProjectPlannerEditForm_userId"><?php echo Yii::t("app", "User"); ?></label>
                    <div class="controls">
                        <select class="input-xlarge" id="ProjectPlannerEditForm_userId" name="ProjectPlannerEditForm[userId]" onchange="admin.planner.onFormChange(this);">
                            <option value="0"><?php echo Yii::t("app", "Please select..."); ?></option>

                            <?php foreach ($users as $user): ?>
                                <option value="<?php echo $user["id"]; ?>"><?php echo CHtml::encode($user["name"] ? $user["name"] : $user["email"]); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="control-group">
                    <label class="control-label" for="ProjectPlannerEditForm_clientId"><?php echo Yii::t("app", "Client"); ?></label>
                    <div class="controls">
                        <select class="input-xlarge" id="ProjectPlannerEditForm_clientId" name="ProjectPlannerEditForm[clientId]" onchange="admin.planner.onFormChange(this);">
                            <option value="0"><?php echo Yii::t("app", "Please select..."); ?></option>

                            <?php foreach ($clients as $client): ?>
                                <option value="<?php echo $client["id"]; ?>"><?php echo CHtml::encode($client["name"]); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="ProjectPlannerEditForm_projectId"><?php echo Yii::t("app", "Project"); ?></label>
                    <div class="controls">
                        <select class="input-xlarge" id="ProjectPlannerEditForm_projectId" name="ProjectPlannerEditForm[projectId]" onchange="admin.planner.onFormChange(this);">
                            <option value="0"><?php echo Yii::t("app", "Please select..."); ?></option>
                        </select>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="ProjectPlannerEditForm_targetId"><?php echo Yii::t("app", "Target"); ?></label>
                    <div class="controls">
                        <select class="input-xlarge" id="ProjectPlannerEditForm_targetId" name="ProjectPlannerEditForm[targetId]" onchange="admin.planner.onFormChange(this);">
                            <option value="0"><?php echo Yii::t("app", "Please select..."); ?></option>
                        </select>
                    </div>
                </div>
                
                <div class="control-group">
                    <label class="control-label" for="ProjectPlannerEditForm_categoryId"><?php echo Yii::t("app", "Category"); ?></label>
                    <div class="controls">
                        <select class="input-xlarge" id="ProjectPlannerEditForm_categoryId" name="ProjectPlannerEditForm[categoryId]" onchange="admin.planner.onFormChange(this);">
                            <option value="0"><?php echo Yii::t("app", "Please select..."); ?></option>
                        </select>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal"><?php echo Yii::t("app", "Cancel"); ?></button>
        <button id="add-button" class="btn btn-primary" onclick="admin.planner.addFormSubmit();"><?php echo Yii::t("app", "Add"); ?></button>
    </div>
</div>

<script>
    $(function () {
        $(".planner").planner({
            dataUrl: "<?php echo $this->createUrl("planner/data"); ?>",
            controlUrl: "<?php echo $this->createUrl("planner/control"); ?>",
            editUrl: "<?php echo $this->createUrl("planner/edit"); ?>"
        });

        $(".datepicker").datepicker();
    });
</script>