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
        <script src="<?php echo Yii::app()->request->baseUrl; ?>/js/bootstrap/bootstrap.alerts-queue.js"></script>
        <script src="<?php echo Yii::app()->request->baseUrl; ?>/js/system.js"></script>
        <script src="<?php echo $this->createUrl('app/constants') . '?' . rand(); ?>"></script>
        <script src="<?php echo $this->createUrl('app/l10n') . '?' . rand(); ?>"></script>
        <script>
            $(function () {
                system.csrf = '<?php echo Yii::app()->request->csrfToken; ?>';
            });
        </script>

        <!-- MxGraph Library -->
        <script type="text/javascript">
            mxBasePath = "<?php echo Yii::app()->request->baseUrl; ?>/js/mxgraph/src";
        </script>
        <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/mxgraph/src/js/mxClient.js"></script>
        <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/mxgraph/grapheditor/grapheditor.js"></script>
        <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/mxgraph/grapheditor/styles.js"></script>

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

                            <?php if (!User::checkRole(User::ROLE_CLIENT) || Yii::app()->user->getShowReports()): ?>
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
                            <?php endif; ?>

                            <?php if (User::checkRole(User::ROLE_ADMIN)): ?>
                                <li class="dropdown <?php if (Yii::app()->controller->id == "planner" || Yii::app()->controller->id == "timetracker" && Yii::app()->controller->action->id == "index") echo "active"; ?>">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                        <?php echo Yii::t("app", "Planning"); ?>
                                        <b class="caret"></b>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li <?php if (Yii::app()->controller->id == "planner") echo 'class="active"'; ?>><a href="<?php echo $this->createUrl("planner/index"); ?>"><?php echo Yii::t('app', "Project Planner"); ?></a></li>
                                        <li <?php if (Yii::app()->controller->id == "timetracker" && Yii::app()->controller->action->id == "index") echo 'class="active"'; ?>><a href="<?php echo $this->createUrl("timetracker/index"); ?>"><?php echo Yii::t('app', "Time Tracker"); ?></a></li>
                                    </ul>
                                </li>
                            <?php endif; ?>

                            <li <?php if (Yii::app()->controller->id == 'vulntracker') echo 'class="active"'; ?>><a href="<?php echo $this->createUrl('vulntracker/index'); ?>"><?php echo Yii::t('app', 'Vulnerability Tracker'); ?></a></li>

                            <?php if (User::checkRole(User::ROLE_USER)): ?>
                                <li <?php if (Yii::app()->controller->id == 'client') echo 'class="active"'; ?>><a href="<?php echo $this->createUrl('client/index'); ?>"><?php echo Yii::t('app', 'Clients'); ?></a></li>
                            <?php endif; ?>

                            <?php if (User::checkRole(User::ROLE_ADMIN)): ?>
                                <li class="dropdown <?php if (in_array(Yii::app()->controller->id, array('backup', 'check', 'reference', 'risk', 'user', 'package', 'reporttemplate', 'monitor', 'history', 'update', 'settings'))) echo 'active'; ?>">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                        <?php echo Yii::t('app', 'System'); ?>
                                        <b class="caret"></b>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li <?php if (Yii::app()->controller->id == 'check') echo 'class="active"'; ?>><a href="<?php echo $this->createUrl('check/index'); ?>"><?php echo Yii::t('app', 'Checks'); ?></a></li>
                                        <li <?php if (Yii::app()->controller->id == 'checklisttemplate') echo 'class="active"'; ?>><a href="<?php echo $this->createUrl('checklisttemplate/index'); ?>"><?php echo Yii::t('app', 'Checklist Templates'); ?></a></li>
                                        <li <?php if (Yii::app()->controller->id == 'relationtemplate') echo 'class="active"'; ?>><a href="<?php echo $this->createUrl('relationtemplate/index'); ?>"><?php echo Yii::t('app', 'Relation Templates'); ?></a></li>
                                        <li <?php if (Yii::app()->controller->id == 'reference') echo 'class="active"'; ?>><a href="<?php echo $this->createUrl('reference/index'); ?>"><?php echo Yii::t('app', 'References'); ?></a></li>
                                        <li <?php if (Yii::app()->controller->id == 'user') echo 'class="active"'; ?>><a href="<?php echo $this->createUrl('user/index'); ?>"><?php echo Yii::t('app', 'Users'); ?></a></li>
                                        <li <?php if (Yii::app()->controller->id == 'package') echo 'class="active"'; ?>><a href="<?php echo $this->createUrl('package/index'); ?>"><?php echo Yii::t('app', 'Packages'); ?></a></li>
                                        <li class="divider"></li>
                                        <li <?php if (Yii::app()->controller->id == 'reporttemplate') echo 'class="active"'; ?>><a href="<?php echo $this->createUrl('reporttemplate/index'); ?>"><?php echo Yii::t('app', 'Report Templates'); ?></a></li>
                                        <li <?php if (Yii::app()->controller->id == 'risk') echo 'class="active"'; ?>><a href="<?php echo $this->createUrl('risk/index'); ?>"><?php echo Yii::t('app', 'Risk Matrix Templates'); ?></a></li>
                                        <li class="divider"></li>
                                        <li <?php if (Yii::app()->controller->id == 'monitor' && Yii::app()->controller->action->id == 'processes') echo 'class="active"'; ?>><a href="<?php echo $this->createUrl('monitor/processes'); ?>"><?php echo Yii::t('app', 'Running Processes'); ?></a></li>
                                        <li <?php if (Yii::app()->controller->id == 'monitor' && Yii::app()->controller->action->id == 'sessions') echo 'class="active"'; ?>><a href="<?php echo $this->createUrl('monitor/sessions'); ?>"><?php echo Yii::t('app', 'Active Sessions'); ?></a></li>
                                        <li <?php if (Yii::app()->controller->id == 'monitor' && Yii::app()->controller->action->id == 'errors') echo 'class="active"'; ?>><a href="<?php echo $this->createUrl('monitor/errors'); ?>"><?php echo Yii::t('app', 'Background Errors'); ?></a></li>
                                        <li <?php if (Yii::app()->controller->id == 'history' && Yii::app()->controller->action->id == 'logins') echo 'class="active"'; ?>><a href="<?php echo $this->createUrl('history/logins'); ?>"><?php echo Yii::t('app', 'Login History'); ?></a></li>
                                        <li class="divider"></li>
                                        <li <?php if (Yii::app()->controller->id == 'backup' && Yii::app()->controller->action->id == 'index') echo 'class="active"'; ?>><a href="<?php echo $this->createUrl('backup/index'); ?>"><?php echo Yii::t('app', 'Backup'); ?></a></li>
                                        <li <?php if (Yii::app()->controller->id == 'backup' && Yii::app()->controller->action->id == 'restore') echo 'class="active"'; ?>><a href="<?php echo $this->createUrl('backup/restore'); ?>"><?php echo Yii::t('app', 'Restore'); ?></a></li>
                                        <li class="divider"></li>
                                        <li <?php if (Yii::app()->controller->id == 'update') echo 'class="active"'; ?>><a href="<?php echo $this->createUrl('update/index'); ?>"><?php echo Yii::t('app', 'Update'); ?></a></li>
                                        <li <?php if (Yii::app()->controller->id == 'settings') echo 'class="active"'; ?>><a href="<?php echo $this->createUrl('settings/edit'); ?>"><?php echo Yii::t('app', 'Settings'); ?></a></li>
                                    </ul>
                                </li>
                            <?php endif; ?>

                            <li class="dropdown <?php if (Yii::app()->controller->id == "account") echo 'active'; ?>">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <?php echo Yii::t("app", "Account"); ?>
                                    <b class="caret"></b>
                                </a>
                                <ul class="dropdown-menu">
                                    <li <?php if (Yii::app()->controller->action->id == "edit") echo 'class="active"'; ?>><a href="<?php echo $this->createUrl('account/edit'); ?>"><?php echo Yii::t('app', 'Settings'); ?></a></li>
                                    <li <?php if (Yii::app()->controller->action->id == "time" ) echo 'class="active"'; ?>><a href="<?php echo $this->createUrl('account/time'); ?>"><?php echo Yii::t('app', 'Time Tracker'); ?></a></li>
                                </ul>
                            </li>

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
                <div class="navigation panel-wrapped">
                    <ul class="breadcrumb inline">
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
                    <div class="btn-group btn-time-records inline">
                        <a class="<?php if (!$this->timeRecords) print "disabled"; ?>" data-toggle="dropdown" href="#" title="<?= Yii::t("app", "Previous Time Records"); ?>">
                            <i class="icon icon-time"></i>
                        </a>
                        <ul class="dropdown-menu time-records-list">
                            <?php if ($this->timeRecords): ?>
                                <li class="time-record-row">
                                    <table class="table">
                                        <tr>
                                            <th colspan="3">
                                                <?= Yii::t("app", "Previous Time Records"); ?>
                                            </th>
                                        </tr>

                                        <?php foreach ($this->timeRecords as $record): ?>
                                            <tr>
                                                <td class="interval">
                                                    <?php print $record['create_time'] ?>
                                                    <?php print $record['start_time']; ?> - <?php print $record['stop_time']; ?>
                                                </td>
                                                <td class="project">
                                                    <a href="<?= $this->createUrl("project/view", array("id" => $record["project_id"])); ?>" target="_blank"><?php print $record['project']; ?></a>
                                                </td>
                                                <td class="total">
                                                    <?php print $record['total']; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </table>
                                </li>
                                <li class="time-record-row">
                                    <table>
                                        <tr>
                                            <td class="btn-view-all">
                                                <a href="<?php print $this->createUrl("account/time"); ?>" target="_blank">View All &raquo;</a>
                                            </td>
                                        </tr>
                                    </table>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <div class="time-session-counter inline">
                        <div class="counter inline">
                            <span class="counter-part hours"><?php $this->timeSession ? print $this->timeSession->duration['hours'] : print "00"; ?></span>:<span class="counter-part minutes"><?php $this->timeSession ? print $this->timeSession->duration['mins'] : print "00"; ?></span>:<span class="counter-part seconds"><?php $this->timeSession ? print $this->timeSession->duration['seconds'] : print "00"; ?></span>
                        </div>
                        <div class="session-controls inline">
                            <div class="start-control <?php if ($this->timeSession) print "hide"; ?>">
                                <a href="#start" onclick="user.timesession.start('<?php print $this->createUrl("account/controltimerecord"); ?>');">
                                    <i class="icon icon-play"></i>
                                </a>
                                <div class="modal fade" id="time-session-project-select" tabindex="-1" role="dialog" aria-labelledby="smallModal" aria-hidden="true">
                                    <?php
                                        $projectIdToSelect = null;

                                        if (Yii::app()->controller->id == 'project') {
                                            $projectIdToSelect = Yii::app()->getRequest()->getQuery('id');
                                        }

                                        if ($this->timeSession) {
                                            $projectIdToSelect = $this->timeSession->project_id;
                                        }
                                    ?>
                                    <div class="modal-dialog modal-sm">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">×</button>
                                                <h3>Select Project</h3>
                                            </div>
                                            <div class="modal-body">
                                                <select class="time-session-project">
                                                    <option value="0">Please select...</option>

                                                    <?php foreach ($this->projects as $project): ?>
                                                        <option value="<?php print $project->id; ?>" <?php if ($projectIdToSelect == $project->id) print 'selected="selected"'; ?>>
                                                            <?php print $project->name; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="modal-footer">
                                                <a href="#" class="btn btn-primary" onclick="user.timesession.start('<?php print $this->createUrl("account/controltimerecord")?>')">Start</a>
                                                <a href="#" class="btn" data-dismiss="modal" onclick="$('.time-session-project').find(':selected').removeAttr('selected');">Cancel</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="stop-control <?php if (!$this->timeSession) print "hide"; ?>">
                                <a href="#stop" onclick="user.timesession.stop('<?php print $this->createUrl("account/controltimerecord"); ?>');">
                                    <i class="icon icon-stop"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="message-container">
                <?php
                    $flashes = Yii::app()->user->getFlashes();
                    if (count($flashes) > 0):
                ?>
                    <script>
                        <?php foreach ($flashes as $key => $message): ?>
                            system.addAlert('<?php echo $key; ?>', '<?php echo str_replace("'", "\\'", $message); ?>');
                        <?php endforeach; ?>
                    </script>
                <?php elseif (User::checkRole(User::ROLE_ADMIN) && $this->_system->update_version && (!isset(Yii::app()->request->cookies["update_version"]) || Yii::app()->request->cookies["update_version"]->value != $this->_system->update_version) && Yii::app()->controller->id != 'update'): ?>
                    <div class="alert alert-info">
                        <?php echo Yii::t("app", "GTTA {version} is available, please update the system.", array("{version}" => $this->_system->update_version)); ?>
                        <a href="<?php echo $this->createUrl("update/index"); ?>"><?php echo Yii::t("app", "Update now"); ?></a>.
                    </div>
                <?php endif; ?>
            </div>

            <?php echo $content; ?>

            <div class="clearfix"></div>

            <hr>
            
            <footer>
                <?php if (!Yii::app()->user->isGuest): ?>
                    <div class="pull-right">
                        <a href="http://community.gtta.net" target="_blank">[<?php echo Yii::t("app", "COMMUNITY"); ?>]</a>
                        &nbsp;<a href="<?php echo $this->createUrl("app/help"); ?>" target="_blank">[<?php echo Yii::t("app", "HELP"); ?>]</a>
                    </div>
                <?php endif; ?>

                <?php echo Yii::t('app', 'Copyright'); ?> &copy; <?php echo date('Y'); ?> <?php echo $this->_system->copyright; ?><br>
                <?php echo Yii::t('app', 'All Rights Reserved'); ?><br>
            </footer>
        </div>
        <?php if ($this->timeSession): ?>
            <script>
                user.timesession.startCounter();
            </script>
        <?php endif; ?>
        <script>
            try {
                console.log('Page generated in', <?php echo $this->_requestTime; ?>, 'seconds');
            } catch (e) {
                // pass
            }
        </script>
    </body>
</html>