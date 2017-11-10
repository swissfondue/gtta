<?php

return [
    // account
    "<action:(login|logout|verify)>"  => "app/<action>",
    "account"                         => "account/edit",
    "account/certificate"             => "account/certificate",
    "account/restore"                 => "account/restore",
    "account/restore/<code:[a-z\d]+>" => "account/changepassword",
    "account/time/<page:\d+>"         => "account/time",
    "account/time"                    => "account/time",
    "account/time/control"            => "account/controltimerecord",

    // misc
    "app/l10n.js" => "app/l10n",
    "app/constants.js" => "app/constants",
    "app/object-list" => "app/objectlist",
    "app/logo" => "app/logo",
    "app/help" => "app/help",
    "app/file/<section:[a-z\-]+>/<subsection:[a-z\-]+>/<file:[a-z0-9\-\.]+>" => "app/file",

    // projects
    "projects/<page:\d+>" => "project/index",
    "projects" => "project/index",
    "project/<id:\d+>/<page:\d+>" => "project/view",
    "project/<id:\d+>" => "project/view",
    "project/<id:\d+>/edit" => "project/edit",
    "project/new" => "project/edit",
    "project/control" => "project/control",

    // project details
    "project/<id:\d+>/details/<page:\d+>"       => "project/details",
    "project/<id:\d+>/details"                  => "project/details",
    "project/<id:\d+>/detail/<detail:\d+>/edit" => "project/editdetail",
    "project/<id:\d+>/detail/new"               => "project/editdetail",
    "project/detail/control"                    => "project/controldetail",

    //project time
    "project/<id:\d+>/time" => "project/time",
    "project/<id:\d+>/time/<page:\d+>" => "project/time",
    "project/<id:\d+>/time/new" => "project/tracktime",
    "project/<id:\d+>/time/control" => "project/controltime",

    // project users
    "project/<id:\d+>/users/<page:\d+>" => "project/users",
    "project/<id:\d+>/users" => "project/users",
    "project/<id:\d+>/user/<user:\d+>" => "project/edituser",
    "project/<id:\d+>/user/new" => "project/edituser",
    "project/<id:\d+>/user/control" => "project/controluser",

    // project target and checks
    "project/<id:\d+>/target/<target:\d+>/<page:\d+>" => "project/target",
    "project/<id:\d+>/target/<target:\d+>" => "project/target",
    "project/<id:\d+>/target/<target:\d+>/edit" => "project/edittarget",
    "project/<id:\d+>/target/new" => "project/edittarget",
    "project/<id:\d+>/target/addlist" => "project/addtargetlist",
    "project/<id:\d+>/target/import" => "project/importtarget",
    "project/<id:\d+>/target/import/mapping/<mId:\d+>" => "project/editmapping",
    "project/mapping/apply" => "project/applymapping",
    "project/<id:\d+>/target/<target:\d+>/chain/edit" => "project/editchain",
    "project/<id:\d+>/target/<target:\d+>/chain/control" => "project/controlchain",
    "project/<id:\d+>/target/<target:\d+>/chain/messages" => "project/chainmessages",
    "project/<id:\d+>/target/<target:\d+>/category/<category:\d+>/control/<controlToOpen:\d+>/check/<checkToOpen:\d+>" => "project/checks",
    "project/<id:\d+>/target/<target:\d+>/category/<category:\d+>" => "project/checks",
    "project/<id:\d+>/target/<target:\d+>/category/control" => "project/controlcategory",
    "project/<id:\d+>/target/<target:\d+>/category/<category:\d+>/control/<control:\d+>" => "project/controlchecklist",
    "project/<id:\d+>/target/<target:\d+>/category/<category:\d+>/check/<check:\d+>" => "project/check",
    "project/<id:\d+>/target/<target:\d+>/category/<category:\d+>/check/<check:\d+>/save" => "project/savecheck",
    "project/<id:\d+>/target/<target:\d+>/category/<category:\d+>/check/<check:\d+>/autosave" => "project/autosavecheck",
    "project/<id:\d+>/target/<target:\d+>/category/<category:\d+>/check/<check:\d+>/control" => "project/controlcheck",
    "project/<id:\d+>/target/<target:\d+>/category/<category:\d+>/check/<check:\d+>/copy" => "project/copycheck",
    "project/<id:\d+>/target/<target:\d+>/category/<category:\d+>/update" => "project/updatechecks",
    "project/<id:\d+>/target/<target:\d+>/category/<category:\d+>/check/<check:\d+>/attachment/new" => "project/uploadattachment",
    "project/attachment/<path:[a-z\d]+>/download" => "project/attachment",
    "project/attachment/control" => "project/controlattachment",
    "project/target/control" => "project/controltarget",
    "project/search" => "project/search",
    "project/target/<target:\d+>/check/<check:\d+>/link" => "project/checklink",
    "project/target/running-checks" => "project/runningchecks",

    // project issues
    "project/<id:\d+>/issues" => "project/issues",
    "project/<id:\d+>/issue/new" => "project/addIssue",
    "project/<id:\d+>/issue/<issue:\d+>" => "project/issue",
    "project/issue/control" => "project/controlissue",
    "project/<id:\d+>/check/search" => "project/searchchecks",
    "project/<id:\d+>/issue/<issue:\d+>/target/search" => "project/searchtargets",
    "project/<id:\d+>/issue/<issue:\d+>/evidence/new" => "project/addEvidence",
    "project/<id:\d+>/issue/<issue:\d+>/evidence/<evidence:\d+>" => "project/evidence",
    "project/<id:\d+>/issue/<issue:\d+>/evidence/<evidence:\d+>/update" => "project/updateevidence",
    "project/<id:\d+>/issue/<issue:\d+>/evidence/update" => "project/updateevidence",
    "project/<id:\d+>/issue/<issue:\d+>/evidence/view/<host:.+>" => "project/viewevidence",
    "project/<id:\d+>/issue/<issue:\d+>/checks/update" => "project/updateissuechecks",
    "project/<id:\d+>/issue/<issue:\d+>/checks/running" => "project/issuerunningchecks",
    "project/evidence/control" => "project/controlevidence",

    // custom checks
    "project/<id:\d+>/target/<target:\d+>/category/<category:\d+>/custom-check/save" => "project/savecustomcheck",
    "project/<id:\d+>/target/<target:\d+>/category/<category:\d+>/custom-check/control" => "project/controlcustomcheck",
    "project/<id:\d+>/target/<target:\d+>/category/<category:\d+>/custom-check/<check:\d+>/attachment/new" => "project/uploadcustomattachment",
    "project/custom-attachment/<path:[a-z\d]+>/download" => "project/customattachment",
    "project/custom-attachment/control" => "project/controlcustomattachment",

    // reports
    "project/<id:\d+>/report/template" => "projectReport/template",
    "project/<id:\d+>/report" => "projectReport/project",
    "project/<id:\d+>/report/rtf" => "projectReport/projectRtf",
    "project/<id:\d+>/report/docx" => "projectReport/projectDocx",
    "project/<id:\d+>/report/rtf/sections" => "projectReport/sections",
    "project/<id:\d+>/report/rtf/sections/save" => "projectReport/saveSection",
    "project/<id:\d+>/report/rtf/sections/save-order" => "projectReport/saveSectionOrder",
    "project/<id:\d+>/report/rtf/sections/control" => "projectReport/controlSection",
    "project/<id:\d+>/report/comparison" => "projectReport/comparison",
    "project/<id:\d+>/report/fulfillment" => "projectReport/fulfillment",
    "project/<id:\d+>/report/risk-matrix" => "projectReport/riskmatrix",
    "project/<id:\d+>/report/vuln-export" => "projectReport/vulnexport",
    "project/<id:\d+>/report/tracked-time" => "projectReport/trackedtime",
    "reports/effort" => "report/effort",

    // project planner
    "project-planner" => "planner/index",
    "project-planner/control" => "planner/control",
    "project-planner/data" => "planner/data",

    // time tracker
    "time-tracker" => "timetracker/index",

    // vulnerability tracker
    "vuln-tracker" => "vulntracker/index",
    "vuln-tracker/<id:\d+>/<page:\d+>" => "vulntracker/vulns",
    "vuln-tracker/<id:\d+>" => "vulntracker/vulns",
    "vuln-tracker/<id:\d+>/target/<target:\d+>/<type:(check|custom)>/<check:\d+>/edit" => "vulntracker/edit",

    // clients
    "clients/<page:\d+>" => "client/index",
    "clients" => "client/index",
    "client/<id:\d+>" => "client/view",
    "client/<id:\d+>/edit" => "client/edit",
    "client/new" => "client/edit",
    "client/control" => "client/control",
    "client/search" => "client/search",
    "client/<id:\d+>/logo/new" => "client/uploadlogo",
    "client/logo/new" => "client/uploadlogo",
    "client/<id:\d+>/logo" => "client/logo",
    "client/logo/control" => "client/controllogo",
    "client/logo/<path:[a-zA-Z0-9]+>" => "client/tmplogo",

    // settings
    "settings" => "settings/edit",
    "settings/integration-key" => "settings/integrationkey",

    // update
    "update" => "update/index",
    "update/status" => "update/status",

    // software packages
    "package/new"                          => "package/new",
    "package/files/<id:\d+>/edit"          => "package/editfiles",
    "package/<id:\d+>/edit"                => "package/editproperties",
    "packages/<page:\d+>"                  => "package/index",
    "packages"                             => "package/index",
    "packages/messages"                    => "package/messages",
    "package/<id:\d+>"                     => "package/view",
    "package/<id:\d+>/share"               => "package/share",
    "package/<id:\d+>/file"                => "package/file",
    "package/control"                      => "package/control",
    "package/upload"                       => "package/upload",
    "packages/regenerate"                  => "package/regenerate",
    "packages/regenerate-status"           => "package/regeneratestatus",
    "packages/sync"                        => "packages/sync",
    "packages/sync-status"                 => "packages/syncStatus",

    // checks
    "checks/<page:\d+>"         => "check/index",
    "checks"                    => "check/index",
    "check/<id:\d+>/<page:\d+>" => "check/view",
    "check/<id:\d+>"            => "check/view",
    "check/<id:\d+>/edit"       => "check/edit",
    "check/new"                 => "check/edit",
    "check/control"             => "check/control",
    "check/search"              => "check/search",
    "check/filter"              => "check/filter",

    // checklist template categories
    "checklist-templates/<page:\d+>"                                   => "checklisttemplate/index",
    "checklist-templates"                                              => "checklisttemplate/index",
    "checklist-template/<id:\d+>/<page:\d+>"                           => "checklisttemplate/viewcategory",
    "checklist-template/<id:\d+>"                                      => "checklisttemplate/viewcategory",
    "checklist-template/<id:\d+>/edit"                                 => "checklisttemplate/editcategory",
    "checklist-template/new"                                           => "checklisttemplate/editcategory",
    "checklist-template/control"                                       => "checklisttemplate/controlcategory",

    // relation templates
    "relation-templates/<page:\d+>"   => "relationtemplate/index",
    "relation-templates"              => "relationtemplate/index",
    "relation-template/<id:\d+>/edit" => "relationtemplate/edit",
    "relation-template/control"       => "relationtemplate/control",

    // checklist templates
    "checklist-template/<id:\d+>/template/<template:\d+>/<page:\d+>"   => "checklisttemplate/viewtemplate",
    "checklist-template/<id:\d+>/template/<template:\d+>/"             => "checklisttemplate/viewtemplate",
    "checklist-template/<id:\d+>/template/<template:\d+>/edit"         => "checklisttemplate/edittemplate",
    "checklist-template/<id:\d+>/template/new"                         => "checklisttemplate/edittemplate",
    "checklist-template/template/control"                              => "checklisttemplate/controltemplate",

    // checklist template checks
    "checklist-template/<id:\d+>/template/<template:\d+>/category/<category:\d+>/edit"  => "checklisttemplate/editcheckcategory",
    "checklist-template/<id:\d+>/template/<template:\d+>/category/new"                  => "checklisttemplate/editcheckcategory",
    "checklist-template/template/<template:\d+>/category/control"                       => "checklisttemplate/controlcheckcategory",

    // check controls
    "check/<id:\d+>/control/<control:\d+>/<page:\d+>" => "check/viewcontrol",
    "check/<id:\d+>/control/<control:\d+>"            => "check/viewcontrol",
    "check/<id:\d+>/control/<control:\d+>/edit"       => "check/editcontrol",
    "check/<id:\d+>/control/new"                      => "check/editcontrol",
    "check/control/control"                           => "check/controlcontrol",

    // checks
    "check/<id:\d+>/control/<control:\d+>/check/<check:\d+>/edit" => "check/editcheck",
    "check/<id:\d+>/control/<control:\d+>/check/new"              => "check/editcheck",
    "check/<id:\d+>/control/<control:\d+>/check/copy"             => "check/copycheck",
    "check/control/check/control"                                 => "check/controlcheck",

    // check results
    "check/<id:\d+>/control/<control:\d+>/check/<check:\d+>/results/<page:\d+>"       => "check/results",
    "check/<id:\d+>/control/<control:\d+>/check/<check:\d+>/results"                  => "check/results",
    "check/<id:\d+>/control/<control:\d+>/check/<check:\d+>/result/<result:\d+>/edit" => "check/editresult",
    "check/<id:\d+>/control/<control:\d+>/check/<check:\d+>/result/new"               => "check/editresult",
    "check/control/check/result/control"                                              => "check/controlresult",

    // check solutions
    "check/<id:\d+>/control/<control:\d+>/check/<check:\d+>/solutions/<page:\d+>"         => "check/solutions",
    "check/<id:\d+>/control/<control:\d+>/check/<check:\d+>/solutions"                    => "check/solutions",
    "check/<id:\d+>/control/<control:\d+>/check/<check:\d+>/solution/<solution:\d+>/edit" => "check/editsolution",
    "check/<id:\d+>/control/<control:\d+>/check/<check:\d+>/solution/new"                 => "check/editsolution",
    "check/control/check/solution/control"                                                => "check/controlsolution",

    // check scripts
    "check/<id:\d+>/control/<control:\d+>/check/<check:\d+>/scripts/<page:\d+>"       => "check/scripts",
    "check/<id:\d+>/control/<control:\d+>/check/<check:\d+>/scripts"                  => "check/scripts",
    "check/<id:\d+>/control/<control:\d+>/check/<check:\d+>/script/<script:\d+>/edit" => "check/editscript",
    "check/<id:\d+>/control/<control:\d+>/check/<check:\d+>/script/new"               => "check/editscript",
    "check/control/check/script/control"                                              => "check/controlscript",

    // check inputs
    "check/<id:\d+>/control/<control:\d+>/check/<check:\d+>/script/<script:\d+>/inputs/<page:\d+>"      => "check/inputs",
    "check/<id:\d+>/control/<control:\d+>/check/<check:\d+>/script/<script:\d+>/inputs"                 => "check/inputs",
    "check/<id:\d+>/control/<control:\d+>/check/<check:\d+>/script/<script:\d+>/input/<input:\d+>/edit" => "check/editinput",
    "check/<id:\d+>/control/<control:\d+>/check/<check:\d+>/script/<script:\d+>/input/new"              => "check/editinput",
    "check/control/check/input/control" => "check/controlinput",

    // check sharing
    "check/share" => "check/share",
    "check/<id:\d+>/share" => "check/sharecategory",
    "check/<id:\d+>/control/<control:\d+>/share" => "check/sharecontrol",
    "check/<id:\d+>/control/<control:\d+>/check/<check:\d+>/share" => "check/sharecheck",

    // references
    "references/<page:\d+>"   => "reference/index",
    "references"              => "reference/index",
    "reference/<id:\d+>/edit" => "reference/edit",
    "reference/new"           => "reference/edit",
    "reference/control"       => "reference/control",

    // report templates
    "report-templates/<page:\d+>" => "reporttemplate/index",
    "report-templates" => "reporttemplate/index",
    "report-template/<id:\d+>/edit" => "reporttemplate/edit",
    "report-template/new" => "reporttemplate/edit",
    "report-template/control" => "reporttemplate/control",
    "report-template/<id:\d+>/header/new" => "reporttemplate/uploadheaderimage",
    "report-template/<id:\d+>/header" => "reporttemplate/headerimage",
    "report-template/header/control" => "reporttemplate/controlheaderimage",
    "report-template/<id:\d+>/rating/<rating:\d+>/new" => "reporttemplate/uploadratingimage",
    "report-template/<id:\d+>/rating/<rating:\d+>" => "reporttemplate/ratingimage",
    "report-template/<id:\d+>/rating/control" => "reporttemplate/controlratingimage",
    "report-template/<id:\d+>/file/new" => "reporttemplate/uploadfile",
    "report-template/<id:\d+>/file" => "reporttemplate/file",
    "report-template/header/file" => "reporttemplate/controlfile",

    // summary blocks
    "report-template/<id:\d+>/summary-blocks/<page:\d+>" => "reporttemplate/summary",
    "report-template/<id:\d+>/summary-blocks" => "reporttemplate/summary",
    "report-template/<id:\d+>/summary-block/<summary:\d+>/edit" => "reporttemplate/editsummary",
    "report-template/<id:\d+>/summary-block/new" => "reporttemplate/editsummary",
    "report-template/summary-block/control" => "reporttemplate/controlsummary",

    // report template vuln sections
    "report-template/<id:\d+>/vuln-sections/<page:\d+>" => "reporttemplate/vulnsections",
    "report-template/<id:\d+>/vuln-sections" => "reporttemplate/vulnsections",
    "report-template/<id:\d+>/vuln-section/<section:\d+>/edit" => "reporttemplate/editvulnsection",
    "report-template/<id:\d+>/vuln-section/new" => "reporttemplate/editvulnsection",
    "report-template/section/control" => "reporttemplate/controlvulnsection",

    // report template sections
    "report-template/<id:\d+>/sections" => "reporttemplate/sections",
    "report-template/<id:\d+>/sections/save" => "reporttemplate/saveSection",
    "report-template/<id:\d+>/sections/save-order" => "reporttemplate/saveSectionOrder",

    // risk classification categories (templates)
    "risks/<page:\d+>"         => "risk/index",
    "risks"                    => "risk/index",
    "risk/<id:\d+>/<page:\d+>" => "risk/view",
    "risk/<id:\d+>"            => "risk/view",
    "risk/<id:\d+>/edit"       => "risk/edit",
    "risk/new"                 => "risk/edit",
    "risk/control"             => "risk/control",

    // risk categories
    "risk/<id:\d+>/category/<category:\d+>/edit" => "risk/editcategory",
    "risk/<id:\d+>/category/new"                 => "risk/editcategory",
    "risk/<id:\d+>/category/control"             => "risk/controlcategory",

    // users
    "users/<page:\d+>"   => "user/index",
    "users"              => "user/index",
    "user/<id:\d+>/edit" => "user/edit",
    "user/new"           => "user/edit",
    "user/control"       => "user/control",
    "user/<id:\d+>/certificate" => "user/certificate",

    // user projects
    "user/<id:\d+>/projects/<page:\d+>" => "user/projects",
    "user/<id:\d+>/projects"            => "user/projects",
    "user/<id:\d+>/project/add"         => "user/addproject",
    "user/<id:\d+>/project/control"     => "user/controlproject",
    "user/<id:\d+>/object-list"         => "user/objectlist",

    // backup
    "backups"                                   => "backup/index",
    "backup/create"                             => "backup/create",
    "<action:(backup|restore)>/check/"          => "backup/check",
    "backup/<filename:[a-z\d\.]+>/download"     => "backup/download",
    "backup/control"                            => "backup/controlbackup",
    "restore"                                   => "backup/restore",

    // system monitor
    "processes"                 => "monitor/processes",
    "process/control"           => "monitor/controlprocess",
    "sessions"                  => "monitor/sessions",
    "errors"                    => "monitor/errors",
    "errors/log"                => "monitor/log",
    "errors/log/clear"          => "monitor/controllog",

    // history
    "history/logins/<page:\d+>" => "history/logins",
    "history/logins"            => "history/logins",

    "customization"                                 => "customization/index",
    "customization/checks/fields"                   => "customization/checkfields",
    "customization/checks/field/<id:\d+>/edit"      => "customization/editcheckfield",
    "customization/checks/field/new"                => "customization/editcheckfield",
    "customization/checks/field/<id:\d+>/control"   => "customization/controlcheckfield",

    "nessus/mappings/<page:\d+>"      => "nessusmapping/index",
    "nessus/mappings"                 => "nessusmapping/index",
    "nessus/mappings/control"         => "nessusmapping/control",
    "nessus/mappings/<id:\d+>/edit"   => "nessusmapping/edit",
    "nessus/mappings/<id:\d+>/search" => "nessusmapping/searchchecks",
    "nessus/mapping/<id:\d+>"         => "nessusmapping/view",
    "nessus/mapping/vuln/update"      => "nessusmapping/updatevuln",
    "nessus/mapping/vuln/filter"      => "nessusmapping/filtervulns",
    "nessus/mapping/activate"         => "nessusmapping/activate",
];