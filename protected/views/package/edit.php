<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery/jquery.ui.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery/jquery.iframe-transport.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery/jquery.fileupload.js"></script>

<div class="active-header">
    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<form class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">
    <input type="hidden" id="PackageEditForm_id" name="PackageEditForm[id]" value="<?php echo $model->id; ?>">

    <fieldset>
        <div class="control-group">
            <label class="control-label"><?php echo Yii::t("app", "Package"); ?></label>
            <div class="controls form-text">
                <div class="file-input">
                    <a href="#logo"><?php echo Yii::t("app", "Upload Package"); ?></a>
                    <input type="file" name="PackageUploadForm[file]" data-upload-url="<?php echo $this->createUrl("package/upload"); ?>" data-package-type="<?php echo $type; ?>">
                </div>

                <div class="upload-message hide"><?php echo Yii::t("app", "Uploading..."); ?></div>
            </div>
        </div>

        <div class="control-group hide">
            <label class="control-label"><?php echo Yii::t("app", "Type"); ?></label>
            <div class="controls form-text" id="package_type"></div>
        </div>

        <div class="control-group hide">
            <label class="control-label"><?php echo Yii::t("app", "Name"); ?></label>
            <div class="controls form-text" id="package_name"></div>
        </div>

        <div class="control-group hide">
            <label class="control-label"><?php echo Yii::t("app", "Version"); ?></label>
            <div class="controls form-text" id="package_version"></div>
        </div>
        
        <div class="form-actions">
            <button type="submit" id="submit_button" class="btn" disabled><?php echo Yii::t("app", "Install"); ?></button>
        </div>
    </fieldset>
</form>

<script>
    $(function () {
        admin.pkg.initUploadForm();
    });
</script>