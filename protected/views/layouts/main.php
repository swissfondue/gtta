<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?php echo Yii::app()->name; ?> - <?php echo CHtml::encode($this->pageTitle); ?></title>
        <meta charset="utf-8">
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/bootstrap.css">
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/bootstrap.datepicker.css">
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/style.css">
        <!--[if lt IE 9]>
            <script src="<?php echo Yii::app()->request->baseUrl; ?>/js/html5.js"></script>
        <![endif]-->
        <script src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.js"></script>
        <script src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.cookie.js"></script>
        <script src="<?php echo Yii::app()->request->baseUrl; ?>/js/bootstrap.js"></script>
        <script src="<?php echo Yii::app()->request->baseUrl; ?>/js/bootstrap.datepicker.js"></script>
        <script src="<?php echo Yii::app()->request->baseUrl; ?>/js/system.js"></script>
        <script src="<?php echo $this->createUrl('app/l10n') . '?' . rand(); ?>"></script>
        <script>
            $(function(){
                system.csrf = '<?php echo Yii::app()->request->csrfToken; ?>';
            });
        </script>
        <?php if (User::checkRole(User::ROLE_ADMIN)): ?>
            <script src="<?php echo Yii::app()->request->baseUrl; ?>/js/admin.js"></script>
        <?php endif; ?>
        <?php if (User::checkRole(User::ROLE_USER)): ?>
            <script src="<?php echo Yii::app()->request->baseUrl; ?>/js/user.js"></script>
        <?php endif; ?>
        <?php if (User::checkRole(User::ROLE_CLIENT)): ?>
            <script src="<?php echo Yii::app()->request->baseUrl; ?>/js/client.js"></script>
        <?php endif; ?>
    </head>
    <body>
        <div class="navbar">
            <div class="navbar-inner">
                <div class="container">
                    <a class="brand" href="<?php echo Yii::app()->request->baseUrl; ?>/"><?php echo Yii::app()->name; ?></a>

                    <?php if (!Yii::app()->user->isGuest): ?>
                        <ul class="nav">
                            <li <?php if (Yii::app()->controller->id == 'project') echo 'class="active"'; ?>><a href="<?php echo $this->createUrl('project/index'); ?>"><?php echo Yii::t('app', 'Projects'); ?></a></li>

                            <?php if (User::checkRole(User::ROLE_USER)): ?>
                                <li class="dropdown <?php if (Yii::app()->controller->id == 'report') echo 'class="active"'; ?>">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                        <?php echo Yii::t('app', 'Reports'); ?>
                                        <b class="caret"></b>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a href="<?php echo $this->createUrl('report/project'); ?>"><?php echo Yii::t('app', 'Project Report'); ?></a></li>
                                        <li><a href="<?php echo $this->createUrl('report/comparison'); ?>"><?php echo Yii::t('app', 'Projects Comparison'); ?></a></li>
                                    </ul>
                                </li>
                                <li <?php if (Yii::app()->controller->id == 'client') echo 'class="active"'; ?>><a href="<?php echo $this->createUrl('client/index'); ?>"><?php echo Yii::t('app', 'Clients'); ?></a></li>
                            <?php endif; ?>
                            
                            <li <?php if (Yii::app()->controller->id == 'effort') echo 'class="active"'; ?>><a href="<?php echo $this->createUrl('effort/index'); ?>"><?php echo Yii::t('app', 'Effort'); ?></a></li>

                            <?php if (User::checkRole(User::ROLE_ADMIN)): ?>
                                <li class="divider-vertical"></li>
                                <li <?php if (Yii::app()->controller->id == 'check') echo 'class="active"'; ?>><a href="<?php echo $this->createUrl('check/index'); ?>"><?php echo Yii::t('app', 'Checks'); ?></a></li>
                                <li <?php if (Yii::app()->controller->id == 'reference') echo 'class="active"'; ?>><a href="<?php echo $this->createUrl('reference/index'); ?>"><?php echo Yii::t('app', 'References'); ?></a></li>
                                <li <?php if (Yii::app()->controller->id == 'user') echo 'class="active"'; ?>><a href="<?php echo $this->createUrl('user/index'); ?>"><?php echo Yii::t('app', 'Users'); ?></a></li>
                                <li class="divider-vertical"></li>
                            <?php endif; ?>

                            <li><a href="<?php echo $this->createUrl('app/logout'); ?>"><?php echo Yii::t('app', 'Logout'); ?></a></li>
                        </ul>
                    <?php endif; ?>

                    <div class="language-selector pull-right">
                        <ul>
                            <?php
                                $languages = array(
                                    'en' => 'English',
                                    'de' => 'Deutsch',
                                );

                                $active = 'en';

                                if (isset(Yii::app()->language))
                                    $active = Yii::app()->language;

                                foreach ($languages as $code => $name):
                            ?>
                                <li <?php if ($active == $code) echo 'class="active"'; ?>><a href="#set-language" onclick="system.setLanguage('<?php echo $code; ?>');" title="<?php echo $name; ?>"><img src="<?php echo Yii::app()->baseUrl; ?>/images/languages/<?php echo $code; ?>.png" alt="<?php echo $name; ?>"></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <div class="loader-image pull-right hidden-object">
                        <img src="<?php echo Yii::app()->baseUrl; ?>/images/loading.gif" />
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <?php if (!Yii::app()->user->isGuest): ?>
                <ul class="breadcrumb">
                    <?php foreach ($this->breadcrumbs as $text => $link): ?>
                        <li<?php if (!$link) echo ' class="active"'; ?>>
                            <?php if (!$link): ?>
                                <?php echo CHtml::encode($text); ?>
                            <?php else: ?>
                                <a href="<?php echo $link; ?>"><?php echo CHtml::encode($text); ?></a> <span class="divider">/</span>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <div class="message-container"></div>

            <?php
                $flashes = Yii::app()->user->getFlashes();
                if (count($flashes) > 0): 
            ?>
                <script>
                    <?php foreach ($flashes as $key => $message): ?>
                        system.showMessage('<?php echo $key; ?>', '<?php echo $message; ?>');
                    <?php endforeach; ?>
                </script>
            <?php endif; ?>

            <?php echo $content; ?>

            <hr>
            
            <footer>
                <?php echo Yii::t('app', 'Copyright'); ?> &copy; <?php echo date('Y'); ?> <a href="<?php echo Yii::app()->homeUrl; ?>"><?php echo Yii::app()->name; ?></a><br>
                <?php echo Yii::t('app', 'All Rights Reserved'); ?><br>
            </footer>
        </div>
    </body>
</html>