<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?php echo Yii::app()->name; ?> - <?php echo CHtml::encode($this->pageTitle); ?></title>
        <meta charset="utf-8">
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/bootstrap/bootstrap.css">
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/bootstrap/bootstrap.datepicker.css">
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/style.css">
        <!--[if lt IE 9]>
            <script src="<?php echo Yii::app()->request->baseUrl; ?>/js/html5.js"></script>
        <![endif]-->
        <script src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery/jquery.js"></script>
        <script src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery/jquery.cookie.js"></script>
        <script src="<?php echo Yii::app()->request->baseUrl; ?>/js/bootstrap/bootstrap.js"></script>
        <script src="<?php echo Yii::app()->request->baseUrl; ?>/js/bootstrap/bootstrap.datepicker.js"></script>
        <script src="<?php echo Yii::app()->request->baseUrl; ?>/js/system.js"></script>
        <script src="<?php echo $this->createUrl('app/l10n') . '?' . rand(); ?>"></script>
        <script>
            $(function () {
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

                            <li class="dropdown <?php if (Yii::app()->controller->id == 'report') echo 'active'; ?>">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <?php echo Yii::t('app', 'Reports'); ?>
                                    <b class="caret"></b>
                                </a>
                                <ul class="dropdown-menu">
                                    <li <?php if (Yii::app()->controller->action->id == 'project') echo 'class="active"'; ?>><a href="<?php echo $this->createUrl('report/project'); ?>"><?php echo Yii::t('app', 'Project Report'); ?></a></li>
                                    <li <?php if (Yii::app()->controller->action->id == 'comparison') echo 'class="active"'; ?>><a href="<?php echo $this->createUrl('report/comparison'); ?>"><?php echo Yii::t('app', 'Projects Comparison'); ?></a></li>
                                    <li <?php if (Yii::app()->controller->id == 'report' && Yii::app()->controller->action->id == 'vulnexport') echo 'class="active"'; ?>><a href="<?php echo $this->createUrl('report/vulnexport'); ?>"><?php echo Yii::t('app', 'Vulnerability Export'); ?></a></li>
                                    <li <?php if (Yii::app()->controller->action->id == 'fulfillment') echo 'class="active"'; ?>><a href="<?php echo $this->createUrl('report/fulfillment'); ?>"><?php echo Yii::t('app', 'Degree of Fulfillment'); ?></a></li>
                                    <li <?php if (Yii::app()->controller->action->id == 'riskmatrix') echo 'class="active"'; ?>><a href="<?php echo $this->createUrl('report/riskmatrix'); ?>"><?php echo Yii::t('app', 'Risk Matrix'); ?></a></li>
                                    <li class="divider"></li>
                                    <li <?php if (Yii::app()->controller->action->id == 'effort') echo 'class="active"'; ?>><a href="<?php echo $this->createUrl('report/effort'); ?>"><?php echo Yii::t('app', 'Effort Estimation'); ?></a></li>
                                </ul>
                            </li>

                            <li <?php if (Yii::app()->controller->id == 'vulntracker') echo 'class="active"'; ?>><a href="<?php echo $this->createUrl('vulntracker/index'); ?>"><?php echo Yii::t('app', 'Vulnerability Tracker'); ?></a></li>

                            <?php if (User::checkRole(User::ROLE_USER)): ?>
                                <li <?php if (Yii::app()->controller->id == 'client') echo 'class="active"'; ?>><a href="<?php echo $this->createUrl('client/index'); ?>"><?php echo Yii::t('app', 'Clients'); ?></a></li>
                            <?php endif; ?>

                            <?php if (User::checkRole(User::ROLE_ADMIN)): ?>
                                <li class="dropdown <?php if (in_array(Yii::app()->controller->id, array( 'backup', 'check', 'reference', 'risk', 'user' ))) echo 'active'; ?>">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                        <?php echo Yii::t('app', 'System'); ?>
                                        <b class="caret"></b>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li <?php if (Yii::app()->controller->id == 'check') echo 'class="active"'; ?>><a href="<?php echo $this->createUrl('check/index'); ?>"><?php echo Yii::t('app', 'Checks'); ?></a></li>
                                        <li <?php if (Yii::app()->controller->id == 'reference') echo 'class="active"'; ?>><a href="<?php echo $this->createUrl('reference/index'); ?>"><?php echo Yii::t('app', 'References'); ?></a></li>
                                        <li <?php if (Yii::app()->controller->id == 'user') echo 'class="active"'; ?>><a href="<?php echo $this->createUrl('user/index'); ?>"><?php echo Yii::t('app', 'Users'); ?></a></li>
                                        <li class="divider"></li>
                                        <li <?php if (Yii::app()->controller->id == 'reporttemplate') echo 'class="active"'; ?>><a href="<?php echo $this->createUrl('reporttemplate/index'); ?>"><?php echo Yii::t('app', 'Report Templates'); ?></a></li>
                                        <li <?php if (Yii::app()->controller->id == 'risk') echo 'class="active"'; ?>><a href="<?php echo $this->createUrl('risk/index'); ?>"><?php echo Yii::t('app', 'Risk Matrix Templates'); ?></a></li>
                                        <li class="divider"></li>
                                        <li <?php if (Yii::app()->controller->id == 'backup' && Yii::app()->controller->action->id == 'backup') echo 'class="active"'; ?>><a href="<?php echo $this->createUrl('backup/backup'); ?>"><?php echo Yii::t('app', 'Backup'); ?></a></li>
                                        <li <?php if (Yii::app()->controller->id == 'backup' && Yii::app()->controller->action->id == 'restore') echo 'class="active"'; ?>><a href="<?php echo $this->createUrl('backup/restore'); ?>"><?php echo Yii::t('app', 'Restore'); ?></a></li>
                                        <li class="divider"></li>
                                        <li <?php if (Yii::app()->controller->id == 'monitor' && Yii::app()->controller->action->id == 'processes') echo 'class="active"'; ?>><a href="<?php echo $this->createUrl('monitor/processes'); ?>"><?php echo Yii::t('app', 'Running Processes'); ?></a></li>
                                        <li <?php if (Yii::app()->controller->id == 'monitor' && Yii::app()->controller->action->id == 'sessions') echo 'class="active"'; ?>><a href="<?php echo $this->createUrl('monitor/sessions'); ?>"><?php echo Yii::t('app', 'Active Sessions'); ?></a></li>
                                        <li class="divider"></li>
                                        <li <?php if (Yii::app()->controller->id == 'history' && Yii::app()->controller->action->id == 'logins') echo 'class="active"'; ?>><a href="<?php echo $this->createUrl('history/logins'); ?>"><?php echo Yii::t('app', 'Login History'); ?></a></li>
                                    </ul>
                                </li>
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

                    <div class="loader-image pull-right hide">
                        <img src="<?php echo Yii::app()->baseUrl; ?>/images/loading.gif">
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <?php if (!Yii::app()->user->isGuest): ?>
                <ul class="breadcrumb">
                    <?php foreach ($this->breadcrumbs as $link): ?>
                        <li<?php if (!$link[1]) echo ' class="active"'; ?>>
                            <?php if (!$link[1]): ?>
                                <?php echo CHtml::encode($link[0]); ?>
                            <?php else: ?>
                                <a href="<?php echo $link[1]; ?>"><?php echo CHtml::encode($link[0]); ?></a> <span class="divider">/</span>
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
                        system.showMessage('<?php echo $key; ?>', '<?php echo str_replace("'", "\\'", $message); ?>');
                    <?php endforeach; ?>
                </script>
            <?php endif; ?>

            <?php echo $content; ?>

            <hr>
            
            <footer>
                <?php echo Yii::t('app', 'Copyright'); ?> &copy; <?php echo date('Y'); ?> <a href="http://infoguard.com">InfoGuard AG</a><br>
                <?php echo Yii::t('app', 'All Rights Reserved'); ?><br>
            </footer>
        </div>
        <script>
            try
            {
                console.log('Page generated in', <?php echo $this->_requestTime; ?>, 'seconds');
            }
            catch (e)
            {
                // pass
            }
        </script>
    </body>
</html>