<link rel="stylesheet" href="/fancybox/source/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
<script type="text/javascript" src="/fancybox/source/jquery.fancybox.pack.js?v=2.1.5"></script>

<h1><?php echo CHtml::encode($this->pageTitle); ?></h1>

<hr>

<div class="container">
    <div class="row">
        <div class="span12 help">
            <h2>Table Of Contents</h2>
            <ul class="toc">
                <li><a href="#whatsnew">What's New?</a></li>
                <li><a href="#general">General</a></li>
                <li><a href="#metasploit">Metasploit</a></li>
                <li><a href="#custom-reports">Custom Reports</a></li>
                <li>
                    <a href="#guided">Guided Tests</a>

                    <ul>
                        <li><a href="#guided-creating">Creating Guided Test Checks</a></li>
                        <li><a href="#guided-performing">Performing Guided Tests</a></li>
                    </ul>
                </li>
                <li>
                    <a href="#integration">Integration Manual</a>

                    <ul>
                        <li><a href="#integration-general">General</a></li>
                        <li><a href="#integration-package">Package Structure</a></li>
                        <li><a href="#integration-library">Library Package</a></li>
                        <li><a href="#integration-script">Script Package</a></li>
                        <li>
                            <a href="#integration-api">API</a>

                            <ul>
                                <li><a href="#integration-api-low-level">Low Level API</a></li>
                                <li><a href="#integration-api-python">Python API</a></li>
                                <li><a href="#integration-api-perl">Perl API</a></li>
                            </ul>
                        </li>
                        <li><a href="#integration-building">Building Your Package</a></li>
                    </ul>
                </li>
            </ul>

            <p id="whatsnew" class="section">
                <h2>What's New?</h2>

                <p>
                    <b>1.11 (current version)</b>:

                    <ul>
                        <li>Project reports now support Microsoft Office 2013.</li>
                        <li>Network tools in GTTA configuration utility (ifconfig, route, ip, ping, traceroute and iptables).</li>
                        <li>Automated connectivity test after network configuration change.</li>
                        <li>GTTA configuration tool launches on local console login (please refer to manual on how to do that).</li>
                        <li>Safari SSL bug workaround.</li>
                        <li>Numerous bugs fixed.</li>
                    </ul>
                </p>

                <p>
                    <b>1.10</b>:

                    <ul>
                        <li>New script: <em>shell</em> - runs an external shell command.</li>
                        <li>Third-level domain support in <em>dns_find_ns</em> script package.</li>
                        <li>Online help updated.</li>
                    </ul>
                </p>

                <p>
                    <b>1.9</b>:

                    <ul>
                        <li>New script: <em>telnet_banner</em> - show telnet server banner.</li>
                        <li>New script: <em>telnet_bruteforce</em> - bruteforce telnet server's login & password.</li>
                        <li>Integration manual.</li>
                        <li>A lot of bugs fixed.</li>
                    </ul>
                </p>

                <p>
                    <b>1.8</b>:

                    <ul>
                        <li>Logo upload form for new clients.</li>
                        <li>Configurable default system language (see <a href="<?php echo $this->createUrl("settings/edit"); ?>" target="_blank">Settings</a>).</li>
                        <li>Additonal information when using scripts (who has launched the check and when).</li>
                        <li>New rating values (No Test Done, No Vulnerability).</li>
                        <li>Horizontal rating selector in check list.</li>
                        <li>Custom solutions for checks.</li>
                        <li>
                            Targets with ports. Now you can specify a port number in the target field like this:
                            <pre>google.com:443</pre>
                            The system will use that host and port in check scripts.
                        </li>
                        <li>Online help (this document).</li>
                        <li>Numerous bugs fixed.</li>
                    </ul>
                </p>
            </p>

            <p id="general" class="section">
                <h2>General</h2>

                <p>
                    This help currently contains only basic topics. Help will be extended in upcoming releases.
                </p>
            </p>

            <p id="metasploit" class="section">
                <h2>Metasploit</h2>

                <p>
                    There is only 1 script responsible for the whole metasploit integration. In order to create a metasploit check script, please do the following:
                </p>

                <ol>
                    <li>Create an automated check (or choose an existing one) that will hold the metasploit script.</li>
                    <li>Go to the <em>Scripts</em> section of that check.</li>
                    <li>Press <em>New Script</em> button.</li>
                    <li>Choose <b>metasploit</b> package in the package list and hit <em>Save</em>.</li>
                    <li>Now you need to create an input that will hold the metasploit commands. Please go to the <em>Inputs</em> section of the script you just created.</li>
                    <li>Hit <em>New Input</em> button.</li>
                    <li>
                        Setup the input that will hold the metasploit script:
                        <ol>
                            <li><em>Name</em> - the name doesn't actually matter - you can enter anything there (for instance, it can be named as <b>Script</b>).</li>
                            <li><em>Type</em> - set it to <b>Textarea</b></li>
                            <li>
                                <em>Value</em> - enter metasploit commands here - the same commands that you would use for msfconsole command in metasploit.
                                Please note, that the system automatically adds <b>run</b> and <b>exit</b> commands at the end of each script, so the script will be launched automatically.

                                <p>
                                    You can use some variables in your script:
                                </p>

                                <ul>
                                    <li><b>@target</b> - check target</li>
                                    <li>
                                        <b>@argN</b> - a file with values from the other input. <b>N</b> here is a number starting from 0,
                                        so <b>@arg0</b> would be the first input, <b>@arg1</b> would be the second input and so on.
                                    </li>
                                </ul>

                                <p>Here is the example script that does a SSH bruteforce:</p>

                                <pre>use auxiliary/scanner/ssh/ssh_login
set rhosts @target
set userpass_file @arg0
set threads 3</pre>
                            </li>
                            <li><em>Visible</em> - unset the checkbox, so the input won't be visible in the checklist.</li>
                        </ol>

                        <div class="help-images">
                            <a class="fancybox" title="Script Setup" href="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "metasploit", "file" => "1.png")); ?>"><img src="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "metasploit", "file" => "1-small.png")); ?>" alt="Script Setup"></a>
                        </div>

                        Then hit <em>Save</em>.
                    </li>
                    <li>
                        If you have used some additional arguments in your script (<b>@arg0</b>, <b>@arg1</b>, etc.), then you will need to add
                        corresponding inputs to the script. For our example script above you need to specify an additional input that will hold
                        login/password pairs. Please refer to metasploit documentation to find out the required file formats for each module.

                        <div class="help-images">
                            <a class="fancybox" title="Additional Arguments" href="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "metasploit", "file" => "2.png")); ?>"><img src="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "metasploit", "file" => "2-small.png")); ?>" alt="Additional Arguments"></a>
                            <a class="fancybox" title="Inputs" href="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "metasploit", "file" => "3.png")); ?>"><img src="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "metasploit", "file" => "3-small.png")); ?>" alt="Inputs"></a>
                        </div>
                    </li>
                    <li>
                        Now it's time to go to your project, use the check you just created and try to start the script.

                        <div class="help-images">
                            <a class="fancybox" title="Metasploit Run" href="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "metasploit", "file" => "4.png")); ?>"><img src="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "metasploit", "file" => "4-small.png")); ?>" alt="Metasploit Run"></a>
                        </div>
                    </li>
                </ol>
            </p>

            <p id="custom-reports" class="section">
                <h2>Custom Reports</h2>

                <p>
                    Downloads:

                    <ul>
                        <li>
                            <a href="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "custom-reports", "file" => "report.docx")); ?>">Example Report Template</a>
                        </li>
                    </ul>
                </p>

                <p>
                    You can generate project report using a custom Word template. In order to make a proper Word template,
                    you should have a Microsoft Windows machine with Microsoft Word 2010 or more. Please note, that
                    older Microsoft Word versions or Microsoft Word for Mac OS are not suitable for creating custom templates.
                </p>

                <p>
                    First of all, you need to enable the <em>Developer</em> tab in Word - you will need it for inserting
                    variables and lists into the template. Please refer to <a target="_blank" href="http://msdn.microsoft.com/en-us/library/bb608625.aspx">Microsoft Office documentation</a>
                    on how to do this. After that tab is enabled, switch to it and enable the <em>Design Mode</em>.
                    You need this mode to make all lists and variables visible.

                    <div class="help-images">
                        <a class="fancybox" title="Design Mode" href="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "custom-reports", "file" => "1.png")); ?>"><img src="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "custom-reports", "file" => "1-small.png")); ?>" alt="Design Mode"></a>
                    </div>
                </p>

                <p>
                    There are 2 ways to insert data into the template:
                </p>

                <p id="custom-reports-text">
                    <h3>Text Variables</h3>

                    <p>
                        Simple global variables insert some static data into the report. All variables
                        look like a usual text within curly brackets (for example, <em>{variable_name}</em>).
                        The following variables are supported:
                    </p>

                    <ul>
                        <li>{project} - project name</li>
                        <li>{company} - project company name</li>
                        <li>{year} - project year</li>
                        <li>{start_date} - project start date</li>
                        <li>{deadline} - project deadline date</li>
                        <li>{rating} - project rating</li>
                        <li>{date} - current date</li>
                        <li>{time} - current time</li>
                        <li>{admin_name} - project admin name</li>
                        <li>{admin_email} - project admin e-mail address</li>
                        <li>{auditor_name} - current user name</li>
                        <li>{auditor_email} - current user e-mail address</li>
                        <li>{target_count} - project target count</li>
                        <li>{check_count} - project check count</li>
                        <li>{high_check_count} - check count with "High Risk" rating</li>
                        <li>{med_check_count} - check count with "Medium Risk" rating</li>
                        <li>{low_check_count} - check count with "Low Risk" rating</li>
                        <li>{info_check_count} - check count with "Info" rating</li>
                    </ul>
                </p>

                <p id="custom-reports-content-control">
                    <h3>Content Control</h3>

                    <p>
                        Content control blocks are responsible for inserting lists and variables
                        into the report. You can insert content control blocks by pressing the <em>Rich Text Content Control</em>
                        button in the <em>Developer</em> tab.

                        <div class="help-images">
                            <a class="fancybox" title="Rich Text Content Control" href="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "custom-reports", "file" => "2.png")); ?>"><img src="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "custom-reports", "file" => "2-small.png")); ?>" alt="Rich Text Content Control"></a>
                        </div>
                    </p>

                    <p>
                        After the content control is inserted into the template, you should press the <em>Properties</em>
                        button to edit its properties. The title of the content control doesn't affect anything - you can
                        put there anything you want, so you can easily know what that control does. The system uses the
                        field named <em>Tag</em>.

                        <div class="help-images">
                            <a class="fancybox" title="Properties" href="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "custom-reports", "file" => "3.png")); ?>"><img src="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "custom-reports", "file" => "3-small.png")); ?>" alt="Properties"></a>
                        </div>
                    </p>

                    <p>
                         There are 4 different types of control tags which you can use in your templates:
                    </p>

                    <ul>
                        <li>
                            <em>variable</em> - you can use variables to insert content into the report, in conditions
                            and list filters. A variable tag looks like this:

                            <pre>var:SCOPE.NAME</pre>

                            <ul>
                                <li>
                                    <p>
                                        <em>SCOPE</em> - an optional variable visibility scope. <em>Scope</em> is an object,
                                        from which the variable will get its value. The reporting engine uses <em>scopes</em>
                                        to access variables in lists. You can skip the scope specification for
                                        a variable, then the system will take the corresponding value from the current scope.
                                        See the <a href="#scopes-explanation">detailed scopes explanation</a> for more info.
                                    </p>

                                </li>
                                <li>
                                    <em>NAME</em> - variable name. Available variable names depend on object you get that
                                    variable from. See the <a href="#variable-list">full list of all available objects and their variables</a>
                                    for more info.
                                </li>
                            </ul>

                            <p>
                                Example:
                            </p>

                            <pre>var:project.name</pre>
                        </li>

                        <li>
                            <em>list</em> - a list of objects. For example, it could be a list of targets within a project
                            or a list of checks within a control. A list looks as follows:

                            <pre>list:SCOPE.NAME</pre>

                             <ul>
                                <li>
                                    <p>
                                        <em>SCOPE</em> - an optional list visibility scope. <em>Scope</em> is an object,
                                        from which the variable will get its list. The reporting engine uses <em>scopes</em>
                                        to access variables in lists. You can skip the scope specification for
                                        a list, then the system will take the corresponding value from the current scope.
                                        See the <a href="#scopes-explanation">detailed scopes explanation</a> for more info.
                                    </p>

                                </li>
                                <li>
                                    <em>NAME</em> - list name. Available list names depend on object you get that
                                    list from. See the <a href="#variable-list">full list of all available list names</a>
                                    for more info.
                                </li>
                            </ul>

                            You can nest multiple list types into each other, so, for example if you use
                            <em>list:category</em> on the top level, it will contain all categories across all targets within
                            that project. Then you can create a list called <em>list:check</em> within it and that list will
                            contain all checks within that particular category for all targets. The same is for other
                            lists - if you create a target list, which has category list and which has check list, then
                            you will get a checklist for each category within each target. Please refer to a <a href="#variable-list">list of
                            possible object lists</a> below.
                        </li>

                        <li>
                            <em>filter</em> - you can filter lists by using filter on certain list item variable. A list
                            with a filter looks like this:

                            <pre>list:LIST|filter:VARIABLE(VALUE)</pre>

                            <ul>
                                <li>
                                    <em>LIST</em> - the name of a list variable. For example, it could be <em>project.target</em>,
                                    which would refer to the project target list. See the <a href="#variable-list">full list of all available objects and their variables</a>
                                    for more info.
                                </li>
                                <li>
                                    <em>VARIABLE</em> - the name of some variable within the list item. So, if you have a list of checks,
                                    then it should be a check variable, etc. Please note that it's not allowed to use a
                                    scope specifier here, only variable names are allowed. For example, if you have a list of checks,
                                    it could be <em>rating</em>, which would refer to the check rating. You can check a full
                                    list of variables for corresponding objects above, in the "variable" section.
                                </li>
                                <li>
                                    <em>VALUE</em> - the scalar value (number or text), against which the variable is checked.
                                </li>
                            </ul>

                            <br>

                            <p>
                                For example, if you want to have a list of only high-risk checks, then the following tag
                                should be used:
                                <pre>list:check|filter:rating(high)</pre>
                            </p>

                            <p>
                                You can use filters for every list and on every variable, but currently that mostly makes sense
                                only for check lists and check ratings.
                            </p>
                        </li>

                        <li>
                            <em>condition</em> - condition block. If the condition is true, then the text inside this content control
                            is inserted into the report, otherwise the content block is removed. The condition tag looks like this:

                            <pre>if:VARIABLE(VALUE)</pre>

                            <ul>
                                <li>
                                    <em>VARIABLE</em> - the name of some variable. For example, it could be <em>project.rating</em>,
                                    which would refer to the project overall rating. You can check a full list of available variables above,
                                    in the "variable" section.
                                </li>
                                <li>
                                    <em>VALUE</em> - is the value, against which the variable is checked. There are 3 value
                                    types supported:

                                    <ul>
                                        <li>
                                            scalar value (some number or text) - checks if the variable specified
                                            is equal to the value provided. For example, you need to check if project rating
                                            is equal to 5:
                                            <pre>if:project.rating(5)</pre>
                                        </li>
                                        <li>
                                            list of numbers or text values - checks if the variable specified is equal
                                            to one of the values provided. For example, you need to check if id is equal
                                            to 1, 2 or 3:
                                            <pre>if:id(1,2,3)</pre>
                                        </li>
                                        <li>
                                            range of numbers - checks if the variable specified is within the given
                                            range (the range includes both starting and ending values). For example,
                                            you check if rating is >= 2 and <= 5 as follows:
                                            <pre>if:rating(2..5)</pre>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                    </ul>

                    <br>

                    <p id="variable-list">
                        <h3>List of Available Objects, Variables and Lists</h3>
                    </p>

                    <ul>
                        <li>
                            <p>
                                <em>project</em> - this object represents the whole project, for which the report is generated.
                                This is the default scope for the report - in other words, when you use a variable
                                which is outside of any list and without a scope specified, it will get its value from the
                                project object.
                            </p>

                            <p>Variables:</p>

                            <ul>
                                <li>name - project name</li>
                                <li>year - project year</li>
                                <li>rating - project total rating, calculated over all targets</li>
                            </ul>

                            <br>

                            <p>Lists:</p>

                            <ul>
                                <li>target - a list of targets in project</li>
                                <li>category - a list of all categories in all targets in project</li>
                                <li>check - a list of all checks in project</li>
                            </ul>

                            <br>&nbsp;
                        </li>
                        <li>
                            <p>
                                <em>target</em> - this object represents one particular target from the project's target
                                list. You can access this object from within the <em>project.target</em> list.
                            </p>

                            <p>Variables:</p>

                            <ul>
                                <li>host - target host</li>
                                <li>description - target description</li>
                            </ul>

                            <br>

                            <p>Lists:</p>

                            <ul>
                                <li>category - category list for target</li>
                                <li>check - a list of checks for target</li>
                            </ul>

                            <br>&nbsp;
                        </li>
                        <li>
                            <p>
                                <em>category</em> - this object represents one particular category for project's or
                                target's category list. You can access this object from within the <em>project.category</em>
                                list or from <em>target.category</em> list.
                            </p>

                            <p>Variables:</p>

                            <ul>
                                <li>name - category name</li>
                            </ul>

                            <br>

                            <p>Lists:</p>

                            <ul>
                                <li>control - a list of controls within category</li>
                                <li>check - a list of category's checks</li>
                            </ul>

                            <br>&nbsp;
                        </li>
                        <li>
                            <p>
                                <em>control</em> - this object is a single control for category's control list. You can
                                access this kind of object from within the <em>category.control</em> list.
                            </p>

                            <p>Variables:</p>

                            <ul>
                                <li>name - control name</li>
                            </ul>

                            <br>

                            <p>Lists:</p>

                            <ul>
                                <li>check - a list of checks in control</li>
                            </ul>

                            <br>&nbsp;
                        </li>
                        <li>
                            <p>
                                <em>check</em> - the object is a check representation for project's, target's, category's
                                or control's list of checks. You can access this object from <em>project.check</em>,
                                <em>target.check</em>, <em>category.check</em> or <em>control.check</em> lists.
                            </p>

                            <p>Variables:</p>

                            <ul>
                                <li>name - check name</li>
                                <li>background_info - check background info</li>
                                <li>hints - check hints</li>
                                <li>question - check question</li>
                                <li>
                                    rating - check rating (text constant representation). Possible values:

                                    <ul>
                                        <li>high</li>
                                        <li>med</li>
                                        <li>low</li>
                                        <li>info</li>
                                        <li>hidden</li>
                                        <li>no_vuln</li>
                                        <li>none</li>
                                    </ul>
                                </li>
                                <li>rating_name - check rating name (like "High Risk" for high risk, etc.)</li>
                                <li>target - check target host</li>
                                <li>links - check links</li>
                                <li>poc - check proof of concept</li>
                                <li>result - check result</li>
                                <li>reference - check reference name</li>
                                <li>
                                    solution - check solution. If a check has multiple solutions, this variable will
                                    hold a concatenated value.
                                </li>
                            </ul>

                            <br>

                            <p>Lists:</p>

                            <ul>
                                <li>attachment - a list of attachments for check</li>
                            </ul>

                            <br>&nbsp;
                        </li>
                        <li>
                            <p>
                                <em>attachment</em> - the object is an attachment representation for check's attachment
                                list. You can access this object from within the <em>check.attachment</em> list.
                            </p>

                            <p>Variables:</p>

                            <ul>
                                <li>name - attachment file name (with extension)</li>
                                <li>
                                    image - attachment body. You can use this variable to insert the actual attachment
                                    image into the place you need.
                                </li>
                            </ul>
                        </li>
                    </ul>

                    <br>

                    <p id="scopes-explanation">
                        <h3>Scopes Explanation</h3>
                    </p>

                    <p>
                        Scopes mechanism comes into action when you use object lists. For example, when you use some variables within
                        a <em>category</em> list, your current scope will be tied to a <em>category</em> from that list. So, if you use a variable
                        named <em>name</em> without any explicit scope specification inside that block, the system will know that it needs
                        to insert there the name of a particular <em>category</em> from that list.
                        </p>

                    <p>
                        Then, for example, within that <em>category</em> block you need to have a list of <em>controls</em>. And
                        inside the control block you use a variable named <em>name</em> without scope specification.
                        In this case the system will get the value from the particular <em>control</em>, because
                        <em>control</em> is the current variable scope.
                    </p>

                    <p>
                        If you have multiple nested lists, you can access to variables of the upper level lists by specifying
                        the scope modififer. For example, you have 3 nested lists: <em>targets</em> &rarr; <em>categories</em> &rarr; <em>checks</em>.
                        You can access target's variables from the check or category block by specifying the scope, for example:
                        <em>var:target.host</em> (<em>target</em> here is the desired variable scope).
                    </p>

                    <p>
                        The default scope for the report is a <em>project</em> object. In other words,
                        when you use a variable without a scope specified and which is not inside any lists, it will
                        get its value from the <em>project</em> object.
                    </p>

                    <p>
                        Please download the <a href="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "custom-reports", "file" => "report.docx")); ?>">example report template</a> for the working example.
                    </p>
                </p>
            </p>

            <p id="guided" class="section">
                <h2>Guided Tests</h2>

                <p id="guided-creating">
                    <h3>Creating Guided Test Checks</h3>

                    <p>
                        First of all, you will need to create some Guided Test checks in order to be able to use Guided Tests for your project.
                    </p>

                    <ol>
                        <li>
                            Click on <em>System &rarr; Guided Test Templates</em> menu item.

                            <div class="help-images">
                                <a class="fancybox" title="Templates" href="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "guided", "file" => "1.png")); ?>"><img src="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "guided", "file" => "1-small.png")); ?>" alt="Templates"></a>
                            </div>
                        </li>
                        <li>
                            You see a list of Guided Test categories on this page. Press <em>New Category</em> button to create a new category.

                            <div class="help-images">
                                <a class="fancybox" title="Categories" href="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "guided", "file" => "2.png")); ?>"><img src="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "guided", "file" => "2-small.png")); ?>" alt="Categories"></a>
                            </div>
                        </li>
                        <li>
                            Enter the desired category name and hit <em>Save</em> button, then click on the <em>View</em> link for the created category (this link is in the top right corner of the page).

                            <div class="help-images">
                                <a class="fancybox" title="Category" href="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "guided", "file" => "3.png")); ?>"><img src="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "guided", "file" => "3-small.png")); ?>" alt="Category"></a>
                            </div>
                        </li>
                        <li>
                            Now you see a list of types for the category. You can create a new type by pressing the <em>New Type</em> button.

                            <div class="help-images">
                                <a class="fancybox" title="Types" href="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "guided", "file" => "4.png")); ?>"><img src="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "guided", "file" => "4-small.png")); ?>" alt="Types"></a>
                            </div>
                        </li>
                        <li>
                            On this page you should enter the desired type name and hit <em>Save</em>. After type is saved, please press the <em>View</em> link in the top right corner of the page.

                            <div class="help-images">
                                <a class="fancybox" title="Type" href="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "guided", "file" => "5.png")); ?>"><img src="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "guided", "file" => "5-small.png")); ?>" alt="Type"></a>
                            </div>
                        </li>
                        <li>
                            Now you are viewing a list of modules within the type. You can create a new module by hitting the <em>New Module</em> button.

                            <div class="help-images">
                                <a class="fancybox" title="Modules" href="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "guided", "file" => "6.png")); ?>"><img src="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "guided", "file" => "6-small.png")); ?>" alt="Modules"></a>
                            </div>
                        </li>
                        <li>
                            After creating a new module, please click the <em>View</em> link.

                            <div class="help-images">
                                <a class="fancybox" title="Module" href="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "guided", "file" => "7.png")); ?>"><img src="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "guided", "file" => "7-small.png")); ?>" alt="Module"></a>
                            </div>
                        </li>
                        <li>
                            Here you see a list of checks within this module. You can add a check by pressing <em>New Check</em> button.

                            <div class="help-images">
                                <a class="fancybox" title="Checks" href="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "guided", "file" => "8.png")); ?>"><img src="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "guided", "file" => "8-small.png")); ?>" alt="Checks"></a>
                            </div>
                        </li>
                        <li>
                            On the check creation page you can enter the following information:
                            
                            <ul>
                                <li><em>Description</em> – current check description</li>
                                <li><em>Target Description</em> – a brief target description for this check</li>
                                <li><em>Control</em> – a check control that contains the desired check</li>
                                <li><em>Check</em> – check that will be added for this module</li>
                                <li><em>Sort Order</em> – check sorting order within a single module</li>
                                <li><em>Dependency Processor</em> – software that will handle check dependencies for this check. There is only one Dependency Processor available at this time – <b>nmap-port</b> – it reads nmap output and suggests targets for other modules. Choosing this Dependency Processor only makes sense when you select <b>Nmap TCP Port Scan</b> check, for other cases use <b>N/A</b>.</li>
                            </ul>

                            <div class="help-images">
                                <a class="fancybox" title="Check" href="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "guided", "file" => "9.png")); ?>"><img src="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "guided", "file" => "9-small.png")); ?>" alt="Check"></a>
                            </div>
                        </li>
                        <li>If you selected some Dependency Processor for the current check, you will need to define some dependencies for it. Click on the <em>Dependencies</em> link on the top right corner.</li>
                        <li>
                            Now you are on the page with a list of dependencies for the current check. Click <em>New Dependency</em> to create a new dependency.

                            <div class="help-images">
                                <a class="fancybox" title="Dependencies" href="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "guided", "file" => "10.png")); ?>"><img src="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "guided", "file" => "10-small.png")); ?>" alt="Dependencies"></a>
                            </div>
                        </li>
                        <li>
                            Here you see the dependency form.

                            <ul>
                                <li><em>Category</em> – Guided Test category of the dependent module</li>
                                <li><em>Type</em> – type of the dependent module</li>
                                <li><em>Module</em> – the dependent module (dependency processor will suggest targets for this module)</li>
                                <li><em>Condition</em> – if this condition will be true, the dependency processor will suggest new targets for the dependent module. For <b>nmap-port</b> dependency processor this field should contain a port number that should be open. If this port on the target is open, then the dependency processor will add a new module for the project (if it is not added yet) and suggest the target for it.</li>
                            </ul>

                            <div class="help-images">
                                <a class="fancybox" title="Dependency" href="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "guided", "file" => "11.png")); ?>"><img src="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "guided", "file" => "11-small.png")); ?>" alt="Dependency"></a>
                            </div>
                        </li>
                        <li>After saving the dependency, you can go back to the dependency list. You can create as many dependencies as you wish.</li>
                    </ol>
                </p>

                <p id="guided-performing">
                    <h3>Performing Guided Tests</h3>

                    <p>
                        You can run guided tests on any project if it has no check results or attached targets yet. Projects with guided tests are displayed with <i class="icon-hand-right"></i> icon in the list of projects. Here is a list of steps for running guided tests in a project:
                    </p>

                    <ol>
                        <li>
                            Open an empty project or create a new one. You will see a <em>Guided Test</em> button on the top – click it to turn the project into a Guided Test project.

                            <div class="help-images">
                                <a class="fancybox" title="Project" href="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "guided", "file" => "12.png")); ?>"><img src="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "guided", "file" => "12-small.png")); ?>" alt="Project"></a>
                            </div>
                        </li>
                        <li>
                            You will see a module selector for the Guided Test. You should unfold the desired categories and types and select modules that you wish to run for this project.

                            <div class="help-images">
                                <a class="fancybox" title="Module Selector" href="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "guided", "file" => "13.png")); ?>"><img src="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "guided", "file" => "13-small.png")); ?>" alt="Module Selector"></a>
                            </div>
                        </li>
                        <li>
                            After you select modules, please press the <em>Save</em> button below. If you selected any modules, you will see that the <em>Start</em> button will appear next to the <em>Save</em> button. You should press it to start Guided Tests.

                            <div class="help-images">
                                <a class="fancybox" title="Save Module Selector" href="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "guided", "file" => "14.png")); ?>"><img src="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "guided", "file" => "14-small.png")); ?>" alt="Save Module Selector"></a>
                            </div>
                        </li>
                        <li>
                            Now you are on the Guided Test Check page. It’s very similar to usual check page, except some differences.
                            The main difference is that Guided Tests display only 1 check per page. Also there are some additional controls:

                            <ol>
                                <li>Check controller. You can press <em>Back</em> or <em>Forward</em> buttons to navigate through checks within all selected modules. Numbers here display the number of the current check and the total number of checks.</li>
                                <li>Brief task description. You can add or change this text on the module’s check edit page (under <em>Guided Test Templates</em> menu).</li>
                                <li>Standard check controls. You can run or clear the check contents using them.</li>
                            </ol>

                            <div class="help-images">
                                <a class="fancybox" title="Check" href="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "guided", "file" => "15.png")); ?>"><img src="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "guided", "file" => "15-small.png")); ?>" alt="Check"></a>
                            </div>
                        </li>
                        <li>
                            Enter some test details here, as if you are on the usual check. In the example on the screenshots I’m filling in the Nmap TCP check, so I will be able to demonstrate you how check dependencies work. Please note, that if you want to use check/module dependencies you should set the <em>Extract</em> option of the <em>Nmap Port Scan</em> check. Otherwise check dependencies won’t work.

                            <div class="help-images">
                                <a class="fancybox" title="Check" href="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "guided", "file" => "16.png")); ?>"><img src="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "guided", "file" => "16-small.png")); ?>" alt="Check"></a>
                            </div>
                        </li>
                        <li>
                            After running the Nmap check, the check suggests  several additional modules and targets with open 22nd port. If you accept the suggestion, you should click on the ✔ icon, otherwise press the ✖ icon to delete the suggestion. After dealing with all suggested targets (if any), you should refresh the page and see if the system added any additional check modules to the current project. In our case, the system adds <em>Internal Penetration Test</em> module to the project with 1 check in it, so you can press <em>Next</em> button in the Guided Test Navigation menu.

                            <div class="help-images">
                                <a class="fancybox" title="Suggested Targets" href="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "guided", "file" => "17.png")); ?>"><img src="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "guided", "file" => "17-small.png")); ?>" alt="Suggested Targets"></a>
                            </div>
                        </li>
                        <li>
                            Here you will see that the system suggest you 2 additional targets. It also specifies the check that suggested these targets. If you wish, you can copy & paste these targets into the target field and perform all required checks, otherwise you can go to the check that suggested these targets and delete the suggestions.

                            <div class="help-images">
                                <a class="fancybox" title="Approved Suggested Targets" href="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "guided", "file" => "18.png")); ?>"><img src="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "guided", "file" => "18-small.png")); ?>" alt="Approved Suggested Targets"></a>
                            </div>
                        </li>
                    </ol>
                </p>
            </p>

            <p id="integration" class="section">
                <h2>Integration Manual</h2>

                <p id="integration-general">
                    <h3>General</h3>

                    <p>
                        GTTA uses the unique packaging system to manage scripts that are used in checks.
                        The system supports 2 types of packages - script and library packages.
                    </p>

                    <ul>
                        <li>
                            <em>Library Package</em> - can be used by <em>Script Packages</em> or other <em>Library Packages</em>.
                            Usually it provides some common functions that can be useful for several scripts or libraries (for example, website crawler).
                        </li>
                        <li>
                            <em>Script Package</em> - the script that performs test or attack actions. User can launch the script by linking it to a particular automated
                            check and pressing the <em>Start</em> button on this check in some project that uses that check.
                        </li>
                    </ul>

                    <p>
                        GTTA provides a lot of useful libraries and scripts out of the box. The built-in scripts apparently can't cover all possible
                        needs, so you are able to upload your custom libraries and scripts as well. This guide describes all required steps to do that.
                    </p>

                    <p>
                        Currently GTTA supports scripts in Python and Perl programming languages. If you wish to use any other language or even a compiled
                        binary program (yeah, that's possible), you should use either a Python or Perl wrapper for it. All scripts you upload must comply with
                        GTTA API requirements (see the API section below for more information).
                    </p>

                    <p>
                        All scripts run in the jailed environment (the Sandbox) which is separate from the main system, so you may use scripts without being
                        afraid to delete something important. In the worst case your Sandbox can become corrupted (for example, you may accidentally delete
                        some system file). If this happens, you can just regenerate the Sandbox from the <em>System &rarr; Scripts</em> menu - it will delete the old
                        Sandbox and create a new one with all scripts and dependencies installed.
                    </p>
                </p>

                <p id="integration-package">
                    <h3>Package Structure</h3>

                    <p>
                        GTTA package (both library and script) is a folder with some files in it. All packages at least contain 1 file named <em>package.yaml</em>,
                        which contains package descrpition. The package description file is a <a href="http://en.wikipedia.org/wiki/YAML">YAML</a> file.
                    </p>

                    <p>
                        Example <em>package.yaml</em> (all section meanings are described below):
                    </p>

                    <pre>type: library
name: example
version: 1.7
system: false
dependencies:
  library:
    - testlibrary
    - customlibrary
  script:
    - somescript
    - test
  system:
    - tcpdump
    - nikto
  deb:
    - nmap_6.66-1_i386.deb
  python:
    - nltk
    - pybloomfiltermmap
  perl:
    - Net::SSL
    - LWP::UserAgent</pre>

                    <p>
                        The package description file has several sections. All sections are mandatory, unless otherwise specified.
                    </p>

                    <ul>
                        <li>
                            <em>type</em> - package type, either <b>library</b> or <b>script</b>.
                        </li>
                        <li>
                            <em>name</em> - package name, allowed characters - a-z, digits, underline, comma and colon. The name must be in lower case letters. The name
                            must be unique, i.e. there should be no packages with the same name in the system, otherwise the installation will fail.
                        </li>
                        <li>
                            <em>version</em> - package version, any format.
                        </li>
                        <li>
                            <em>system</em> - system package flag, either <b>true</b> or <b>false</b>. All custom packages must have this option set to <b>false</b>, since <b>true</b> is
                            allowed only for built-in packages.
                        </li>
                        <li>
                            <em>dependencies</em> - a list of dependencies for this package. This section is optional - some packages can be fully independent.
                        </li>
                    </ul>

                    <p>
                        As shown above, there can be 6 types of dependencies. All sections described below are optional:
                    </p>

                    <ul>
                        <li>
                            <em>library</em> - here you can specify a list of GTTA libraries required for this package. The libraries specified in this section must be
                            already installed when you package is being installed, otherwise the installation process will fail.
                        </li>
                        <li>
                            <em>script</em> - this section contains a list of GTTA script dependencies. The scripts must be already installed prior to this package
                            installation.
                        </li>
                        <li>
                            <em>system</em> - system dependencies. GTTA uses Debian 6 operating system underneath and this section contains a list of system package
                            dependencies that can be installed from the Debian APT repository.
                        </li>
                        <li>
                            <em>deb</em> - Debian binary package dependencies. Not all required packages can be found in the standard Debian repository, so you can use
                            your custom-built (or downloaded from the internet) Debian binary packages to install the required software. In order to install a
                            custom Debian binary package you should put it to the GTTA package folder (the same folder that contains your <em>package.yaml</em>) and
                            include it to this section of <em>package.yaml</em>. Please note, that the Debian binary package name must conform to the standard Debian
                            naming convention: &lt;name&gt;_&lt;VersionNumber&gt;-&lt;RevisionNumber&gt;_i386.deb. Please refer to this documentation for more info -
                            <a href="http://www.debian.org/doc/manuals/debian-faq/ch-pkg_basics.en.html">http://www.debian.org/doc/manuals/debian-faq/ch-pkg_basics.en.html</a>.
                        </li>
                        <li>
                            <em>python</em> - Python library dependencies that will be installed using <a href="http://www.pip-installer.org/en/latest/">pip package manager</a>. Debian
                            APT repository doesn't contain all available Python libraries and a lot of Python libraries in APT are outdated, so you might want
                            to install recent Python libraries using pip. If you want to install the specific version of a Python package and not the latest one,
                            then please specify the name in the following format - <b>name==version</b> (for example, <b>pybloomfiltermmap==0.2.0</b>).
                        </li>
                        <li>
                            <em>perl</em> - Perl library dependencies that will be installed using <a href="http://www.cpan.org/">CPAN</a>. Unlike Debian APT repository, CPAN contains
                            all available and most recent Perl libraries, so you might want to use it to install Perl libraries.
                        </li>
                    </ul>

                    <p>
                        Please note, that <b>library</b> and <b>script</b> dependecies must be installed manually (using GTTA GUI) prior to your package installation,
                        otherwise the installation will fail. Other dependency sections contain information about software that will be installed automatically
                        during your package installation.
                    </p>
                </p>

                <p id="integration-library">
                    <h3>Library Package</h3>

                    <p>
                        <em>Library Package</em> is a package that provides some services (functions, data, etc.) for other GTTA libraries or scripts. There are no
                        additional requirements for <em>Library Packages</em> - they can contain literally anything - custom Python modules, custom Perl modules,
                        shell scripts, anything. GTTA API for scripts contains methods that will help you to get an absolute filesystem path for a
                        certain library using only its name, so you will be able to use all files in a library from your scripts by including them (if we are
                        talking about Python or Perl modules), by running them or by opening and reading them as regular files.
                    </p>

                    <p>
                        For example, if you have a Python library and you are planning to use it from multiple scripts, it's a good idea to upload that
                        library as a GTTA <em>Library Package</em>, so your <em>Script Packages</em> won't have to provide their own copies of the same library. The same thing
                        is with Perl libraries or custom-built Debian binary packages.
                    </p>
                </p>

                <p id="integration-script">
                    <h3>Script Package</h3>

                    <p>
                        <em>Script Package</em> is a package that contains a script that can be launched by GTTA to do some test or attack. GTTA has very strict
                        requirements for scripts in order to be able to run them and make the developer's life much easier. There are 3 basic requirements
                        for all <em>Script Packages</em>:
                    </p>

                    <ol>
                        <li>
                            The script must be coded in Python 2.7 (preferred) or Perl 5.14 scripting language. You may create a script in any other language, but in this
                            case you must create a Python or Perl wrapper for it.
                        </li>
                        <li>
                            The script must have a predefined entry point script named <em>run.py</em> or <em>run.pl</em> (for Python and Perl scripts, respectively).
                        </li>
                        <li>
                            The script must use GTTA API for the corresponding programming language.
                        </li>
                    </ol>

                    <p>
                        The <em>Script Package</em> may contain any number of additional files. You can do with them anything you need - include them from your script
                        as additional modules, you can read and write them or run them as an external program.
                    </p>
                </p>

                <p id="integration-api">
                    <h3>API</h3>

                    <p>
                        As have been said before, all scripts run in the Sandbox and are not connected to the main system. All changes done by scripts may be
                        lost on Sandbox Regeneration, so don't store any sensitive information from your scripts to any files besides the result file. The
                        result file is transferred to the main system after the script finishes its work, so it's the only safe place to store the information.
                    </p>

                    <p id="integration-api-low-level">
                        <h4>Low Level API</h4>

                        <p>
                            This section describes how scripts work on the lowest level and this information should be used only to understand how the process goes
                            underneath. When you will create your script, you must use GTTA API, since the Low Level API may change in future and your scripts will
                            stop working in the next GTTA versions, but GTTA API will remain the same as long as possible.
                        </p>

                        <p>
                            Scripts are launched in the background mode with certain command line arguments. Every command line argument passed to GTTA script
                            is a path to a file, which contains some input data. An input file is a sequence of lines separated by the Line Feed symbols
                            (LF, 0x0A or \n) and every single line is assumed to be a separate input record.
                        </p>

                        <p>
                            First two command line arguments (i.e. input files) are mandatory and are passed to every GTTA script:
                        </p>

                        <ol>
                            <li>Path to a target file, which describes a target system that should be checked and some basic check settings.</li>
                            <li>Path to an output file that should be used to write the script output.</li>
                        </ol>

                        <p>
                            Other command line arguments are completely optional and depend on the particular check requirements.
                        </p>

                        <p>
                            <b>Target File Structure</b>
                        </p>

                        <p>
                            Here is a list of lines, that describe the target:
                        </p>

                        <ol>
                            <li>
                                <em>IP Address (Host Name)</em> – target's network addres – either IP, or FQDN.
                            </li>
                            <li>
                                <em>Protocol</em> – a name of the protocol, that should be used during this check. For example, if a script performs a web test,
                                this field may have one of these values: http or https. Of course, the script can just silently ignore this option.
                            </li
                            <li>
                                <em>Port Number</em> – a target's port number to connect. This option could be ignored.
                            </li>
                            <li>
                                <em>Language</em> – user selected language code – English or German (en, de). Currently all scripts ignore this option, but if your script
                                needs to output different information depending on the selected language – this option might be useful.
                            </li>
                        </ol>

                        <p>
                            <b>Result File Structure</b>
                        </p>

                        <p>
                            Result file is a sequence of lines, separated by Line Feed symbols. Result file can contain several control sequences, that will be
                            processed by the GUI to be able to display the information for the user in more convenient way. The control sequence is a simple XML
                            text with special tags.
                        </p>

                        <p>
                            <b>Result Tables</b>
                        </p>

                        <p>
                            This control sequence allows displaying tables tied to the check. The control sequence looks like this:
                        </p>

                        <pre>&lt;gtta-table&gt;
    &lt;columns&gt;
        &lt;column width="0.4"&gt;Column Title&lt;/column&gt;
        &lt;column width="0.6"&gt;2nd Column Title&lt;/column&gt;
    &lt;/columns&gt;
    &lt;row&gt;
        &lt;cell&gt;Some Data&lt;/cell&gt;
        &lt;cell&gt;88&lt;/cell&gt;
    &lt;/row&gt;
    &lt;row&gt;
        &lt;cell&gt;Some Other Data&lt;/cell&gt;
        &lt;cell&gt;99&lt;/cell&gt;
    &lt;/row&gt;
&lt;/gtta-table></pre>

                        <p>This example contains several required control tags:</p>

                        <ul>
                            <li><em>columns</em> – contains table column definitions</li>
                            <li>
                                <em>column</em> – defines a name for a single column. Attribute named <em>width</em> contains a width definition for the column.
                                Width is a float number ≤ 1.0 (1.0 is a full table width).
                            </li>
                            <li>
                                <em>row</em> - contains a list of cells for a row.
                            </li>
                            <li>
                                <em>cell</em> – defines a cell content.
                            </li>
                        </ul>

                        <p>Please note that script can return only 1 table per launch.</p>

                        <p>
                            <b>Result Attachments</b>
                        </p>

                        <p>
                            This control sequence allows adding attachments to the check object. The control sequence looks like this:
                        </p>

                        <pre>&lt;gtta-image src="/path/to/file.png"&gt;&lt;/gtta-image&gt;</pre>

                        <p>
                            Attribute <em>src</em> contains a local path to the file to be attached, so the check script is totally responsible for downloading that
                            file to the local host. After attachment is added to a project, the system automatically deletes the source file.
                        </p>

                        <p>
                            The system allows to attach any kind of files, the main tag is called <em>gtta-image</em> because mostly this mechanism will be used for
                            image attachments.
                        </p>
                    </p>

                    <p id="integration-api-python">
                        <h4>Python API</h4>

                        <br>
                        <p>
                            Downloads:

                            <ul>
                                <li>
                                    <a href="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "integration", "file" => "core.zip")); ?>">API Library</a>
                                </li>
                                <li>
                                    <a href="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "integration", "file" => "example-python.zip")); ?>">Python Script Example</a>
                                </li>
                            </ul>
                        </p>

                        <p>
                            Python API is a standard GTTA library which helps to create automated scripts in Python.
                            The library implements some common tasks, such as parsing command line arguments, reading
                            input files and writing to the output file. All Python checks should use this library.
                            The Python API library is the preferred way to do automated check scripts.
                        </p>

                        <p>
                            In order to use the library, a script should import <em>core</em> library into its namespace.
                            The script should declare some class inherited from the base class <em>Task</em>. The ancestor
                            class should implement a function named <em>main</em> – this function should do the script’s job.
                            Function <em>main</em> will be called just after library parsed all command line arguments.
                            The list of function’s parameters depends on additional arguments that have been passed to the script.
                            Here's the example:

                            <pre># coding: utf-8

from core import Task, execute_task

class Example(Task):
    """Example task"""

    def main(self, *args):
        """Main function"""
        self._write_result("Hello there!")

    def test(self):
        """Test function"""
        self.main()

execute_task(Example)</pre>
                        </p>

                        <p>
                            For example, if the script has been called like this:

                            <pre>run.py target.txt result.txt data1.txt data2.txt</pre>

                            then main function will be called with 2 parameters (except self class reference) – the first
                            parameter will contain a list of lines in data1.txt file, the second will contain a list of
                            lines in data2.txt file. If input files are empty, then empty lists will be passed.
                        </p>

                        <p>
                            For example, if the first file contains 2 lines and the second one is empty, a function call
                            may look like this:

                            <pre>main(["first line", "second line"], [])</pre>

                        </p>

                        <p>
                            <b>Attributes</b>
                        </p>

                        <p>
                            <em>Task</em> class has some attributes, which give access to data in the target file:

                            <ul>
                                <li>
                                    <em>self.target</em> – target name (either host name or IP address).
                                </li>
                                <li>
                                    <em>self.host</em> – domain name (if it was specified on the first line of the target file).
                                </li>
                                <li>
                                    <em>self.ip</em> – IP-address (if target file contained one). Only host name or IP address,
                                    but not both can be specified at the same time, since there is only 1 field in the
                                    target file that contains host address data.
                                </li>
                                <li>
                                    <em>self.proto</em> – protocol name.
                                </li>
                                <li>
                                    <em>self.port</em> – port number.
                                </li>
                                <li>
                                    <em>self.lang</em> – language code.
                                </li>
                            </ul>
                        </p>

                        <p>
                            <b>Constants</b>
                        </p>

                        <p>
                            <em>Task</em> class has some constants, which are responsible for how input files are processed
                            and how much time this particular task can take. You can override these constants in your script
                            class:

                            <ul>
                                <li>
                                    <em>TIMEOUT</em> - the maximum amount of time in seconds this script can work. After that
                                    time passes, the script gets killed. The default value is 60 seconds.
                                </li>
                                <li>
                                    <em>TEST_TIMEOUT</em> - the maximum amount of time in seconds this script can work in
                                    test mode. After that time passes, the script gets killed. The default value is 30 seconds.
                                </li>
                                <li>
                                    <em>PARSE_FILES</em> - boolean value, which determines if core library should split
                                    input files by lines. If this constant is <b>True</b> (default), then API reads all
                                    input files into memory and splits them by line. If this constant is <b>False</b>, then
                                    API just passes the file name as input argument. This constant can be useful if you have
                                    big input files that can consume a lot of resources if you read them in memory.
                                </li>
                            </ul>
                        </p>

                        <p>
                            <b>Modules, Classes, Methods and Functions</b>
                        </p>

                        <p>
                            There are several useful functions and methods provided by API that you can use in your scripts:

                            <ul>
                                <li>
                                    <em>core</em> - core module that contains everything needed to create a script.

                                    <ul>
                                        <li>
                                            <em>execute_task(class_name)</em> - function that is used to show
                                            which class contains the main script code. After class declaration, the script should
                                            call this function with the name of your script class as an argument.
                                        </li>
                                        <li>
                                            <em>Task</em> - base class for all scripts.

                                            <ul>
                                                <li>
                                                    <em>Task.main(*args)</em> - main script function. This function implementation is required for
                                                    every script.
                                                </li>
                                                <li>
                                                    <em>Task.test()</em> - you can use this function to test the script without creating
                                                    all the required files. Just run the script like this:
                                                    <pre>run.py --test</pre>
                                                    and the API will call <em>Task.test</em> function instead of <em>Task.main</em>.
                                                </li>
                                                <li>
                                                    <em>Task._write_result(data)</em> - writes <em>data</em> into a result file. One method
                                                    call will produce a single line in the output file.
                                                </li>
                                                <li>
                                                    <em>Task._check_stop()</em> - call this method before and after doing time-consuming or
                                                    blocking operations – it checks if the script should stop execution and exit.
                                                    Scripts can be terminated by user request from GUI, so it’s required to perform this kind of check.
                                                </li>
                                                <li>
                                                    <em>Task._get_library_path(library)</em> - get local filesystem path to a <em>Library Package</em>
                                                    named <em>library</em>. This method is useful when you need to call or use some files from
                                                    the <em>Library Package</em>.
                                                </li>
                                            </ul>
                                        </li>
                                        <li>
                                            <em>ResultTable</em> - result table class, which helps to create Result Tables very easily.

                                            <ul>
                                                <li>
                                                    <em>__init__(columns)</em> - create table with <em>columns</em> as a list of column definitions.
                                                    This list should contain of dictionaries with the following keys:

                                                    <ul>
                                                        <li><em>name</em> - column name</li>
                                                        <li><em>width</em> - column width (0 .. 1, 1 = 100%)</li>
                                                    </ul>

                                                    <p>
                                                        Example:
                                                        <pre>[{"name": "User", "width": 0.5}, {"name": "Count", "width": 0.5}]</pre>
                                                    </p>
                                                </li>
                                                <li>
                                                    <em>add_row(row)</em> - add row with data to a table. <em>row</em> is a list of respective column values.

                                                    <p>
                                                        Example:
                                                        <pre>["John", "733"]</pre>
                                                    </p>
                                                </li>
                                                <li>
                                                    <em>render()</em> - get a rendered XML table. You can get a result of this function and directly write it
                                                    to a result file using <em>Task._write_result</em>.
                                                </li>
                                            </ul>

                                            <p>
                                                <br>
                                                Typical <em>ResultTable</em> usage example:

                                                <pre># create table columns
table = ResultTable([
    {"name": "Number", "width": 0.3},
    {"name": "E-mail", "width": 0.5},
    {"name": "Value", "width": 0.2},
])

# add rows
table.add_row(["1", "john@doe.com", "123"])
table.add_row(["2", "hello@world.com", "666"])
table.add_row(["3", "bob@bob.com", "444"])

# output table to results
self._write_result(table.render())</pre>
                                            </p>
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <em>call</em> - calling external processes.

                                    <ul>
                                        <li>
                                            <em>call(command)</em> <b>[DEPRECATED]</b> - run external <em>command</em>. Returns a list of 2 values:
                                            <ul>
                                                <li>
                                                    <em>ok</em> - boolean value, <b>True</b> if the program has run without any errors,
                                                    <b>False</b> - if program has finished with error.
                                                </li>
                                                <li>
                                                    <em>result</em> - text output of the called program.
                                                </li>
                                            </ul>

                                            <p>
                                                <b>WARNING!</b> This function is deprecated and will be removed in upcoming releases of GTTA.
                                                Please use Python's standard <em>subprocess</em> module instead.
                                            </p>
                                        </li>
                                        <li>
                                            <em>cd(directory)</em> - directory changing context manager. You can use it as follows:
                                            <pre>with cd("/path/to/some/dir"):
    # everything that goes here will be executed under /path/to/some/dir directory
    test_file = open("test.txt", "r") # &lt;-- the program tries to open /path/to/some/dir/test.txt</pre>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </p>

                        <p>
                            <b>Other Requirements</b>
                        </p>

                        <p>
                            <ul>
                                <li>
                                    It's required to implement error checks and try-catch blocks to handle all possible errors.
                                    If any error occurs during the execution, the script should show some information about that issue.
                                </li>
                            </ul>
                        </p>
                    </p>

                    <p id="integration-api-perl">
                        <h4>Perl API</h4>

                        <br>
                        <p>
                            Downloads:

                            <ul>
                                <li>
                                    <a href="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "integration", "file" => "core.zip")); ?>">API Library</a>
                                </li>
                                <li>
                                    <a href="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "integration", "file" => "example-perl.zip")); ?>">Perl Script Example</a>
                                </li>
                            </ul>
                        </p>

                        <p>
                            Perl API is a standard GTTA library which helps to create automated scripts in Perl.
                            The library implements some common tasks, such as parsing command line arguments, reading
                            input files and writing to the output file. All Perl checks should use this library.
                        </p>

                        <p>
                            In order to use the library, a script should import
                            <a href="http://search.cpan.org/~ether/MooseX-Declare-0.38/lib/MooseX/Declare.pm">MooseX::Declare</a>
                            (Perl API uses it for object-oriented interface) and <em>core::task</em> modules. The script should
                            declare some class inherited from the base class <em>Task</em>. The ancestor
                            class should implement a method named <em>main</em> – this function should do the script’s job.
                            Function <em>main</em> will be called just after library parsed all command line arguments.
                            The list of function’s parameters depends on additional arguments that have been passed to the script.
                            Here's the example:

                            <pre>use MooseX::Declare;
use core::task qw(execute);

# Example Perl script
class Example extends Task {
    # Main function
    method main($args) {
        $self->_write_result("Hello there!");
    }

    # Test function
    method test {
        $self->main();
    }
}

execute(Example->new());</pre>
                        </p>

                        <p>
                            For example, if the script has been called like this:

                            <pre>run.pl target.txt result.txt data1.txt data2.txt</pre>

                            then main function will be called with 2 parameters (except self class reference) – the first
                            parameter will contain a list of lines in data1.txt file, the second will contain a list of
                            lines in data2.txt file. If input files are empty, then empty lists will be passed.
                        </p>

                        <p>
                            For example, if the first file contains 2 lines and the second one is empty, a function call
                            may look like this:

                            <pre>main(["first line", "second line"], []);</pre>

                        </p>

                        <p>
                            <b>Attributes</b>
                        </p>

                        <p>
                            <em>Task</em> class has some attributes, which give access to data in the target file:

                            <ul>
                                <li>
                                    <em>$self->target</em> – target name (either host name or IP address).
                                </li>
                                <li>
                                    <em>$self->host</em> – domain name (if it was specified on the first line of the target file).
                                </li>
                                <li>
                                    <em>$self->ip</em> – IP-address (if target file contained one). Only host name or IP address,
                                    but not both can be specified at the same time, since there is only 1 field in the
                                    target file that contains host address data.
                                </li>
                                <li>
                                    <em>$self->proto</em> – protocol name.
                                </li>
                                <li>
                                    <em>$self->port</em> – port number.
                                </li>
                                <li>
                                    <em>$self->lang</em> – language code.
                                </li>
                            </ul>
                        </p>

                        <p>
                            <b>Constants</b>
                        </p>

                        <p>
                            <em>Task</em> class has some constants, which are responsible for how input files are processed
                            and how much time this particular task can take. You can override these constants in your script
                            class:

                            <ul>
                                <li>
                                    <em>TIMEOUT</em> - the maximum amount of time in seconds this script can work. After that
                                    time passes, the script gets killed. The default value is 60 seconds.
                                </li>
                                <li>
                                    <em>TEST_TIMEOUT</em> - the maximum amount of time in seconds this script can work in
                                    test mode. After that time passes, the script gets killed. The default value is 30 seconds.
                                </li>
                                <li>
                                    <em>PARSE_FILES</em> - boolean value, which determines if core library should split
                                    input files by lines. If this constant is <b>1</b> (default), then API reads all
                                    input files into memory and splits them by line. If this constant is <b>0</b>, then
                                    API just passes the file name as input argument. This constant can be useful if you have
                                    big input files that can consume a lot of resources if you read them in memory.
                                </li>
                            </ul>
                        </p>

                        <p>
                            <b>Modules, Classes, Methods and Functions</b>
                        </p>

                        <p>
                            There are several useful functions and methods provided by API that you can use in your scripts:

                            <ul>
                                <li>
                                    <em>core::task</em> - core module that contains everything needed to create a script.

                                    <ul>
                                        <li>
                                            <em>execute(task_object)</em> - function that is used to show
                                            which object contains the main script code. After class declaration, the script should
                                            call this function with the instantiated object of your script class as an argument.
                                        </li>
                                        <li>
                                            <em>Task</em> - base class for all scripts.

                                            <ul>
                                                <li>
                                                    <em>Task->main($args)</em> - main script function. This function implementation is required for
                                                    every script.
                                                </li>
                                                <li>
                                                    <em>Task->test()</em> - you can use this function to test the script without creating
                                                    all the required files. Just run the script like this:
                                                    <pre>run.pl --test</pre>
                                                    and the API will call <em>Task->test</em> function instead of <em>Task->main</em>.
                                                </li>
                                                <li>
                                                    <em>Task->_write_result($data)</em> - writes <em>$data</em> into a result file. One method
                                                    call will produce a single line in the output file.
                                                </li>
                                                <li>
                                                    <em>Task->_check_stop()</em> - call this method before and after doing time-consuming or
                                                    blocking operations – it checks if the script should stop execution and exit.
                                                    Scripts can be terminated by user request from GUI, so it’s required to perform this kind of check.
                                                </li>
                                                <li>
                                                    <em>Task->_get_library_path($library)</em> - get local filesystem path to a <em>Library Package</em>
                                                    named <em>$library</em>. This method is useful when you need to call or use some files from
                                                    the <em>Library Package</em>.
                                                </li>
                                            </ul>
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <em>core::resulttable</em> - Result Table functions and classes.

                                    <ul>
                                        <li>
                                            <em>ResultTable</em> - result table class, which helps to create Result Tables very easily.

                                            <ul>
                                                <li>
                                                    <em>new($columns)</em> - create table with <em>$columns</em> as a list of column definitions.
                                                    This list should contain of hashes with the following keys:

                                                    <ul>
                                                        <li><em>name</em> - column name</li>
                                                        <li><em>width</em> - column width (0 .. 1, 1 = 100%)</li>
                                                    </ul>

                                                    <p>
                                                        Example:
                                                        <pre>[{name => "User", width => 0.5}, {name => "Count", width => 0.5}]</pre>
                                                    </p>
                                                </li>
                                                <li>
                                                    <em>add_row($row)</em> - add row with data to a table. <em>$row</em> is a list of respective column values.

                                                    <p>
                                                        Example:
                                                        <pre>["John", "733"]</pre>
                                                    </p>
                                                </li>
                                                <li>
                                                    <em>render()</em> - get a rendered XML table. You can get a result of this function and directly write it
                                                    to a result file using <em>Task->_write_result()</em>.
                                                </li>
                                            </ul>

                                            <p>
                                                <br>
                                                Typical <em>ResultTable</em> usage example:

                                                <pre># create table columns
$table = ResultTable->new([
    {name => "Number", width => 0.3},
    {name => "E-mail", width => 0.5},
    {name => "Value", width => 0.2},
]);

# add rows
$table->add_row(["1", "john@doe.com", "123"]);
$table->add_row(["2", "hello@world.com", "666"]);
$table->add_row(["3", "bob@bob.com", "444"]);

# output table to results
$self->_write_result($table->render());</pre>
                                            </p>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </p>

                        <p>
                            <b>Other Requirements</b>
                        </p>

                        <p>
                            <ul>
                                <li>
                                    It's required to implement error checks and eval blocks to handle all possible errors.
                                    If any error occurs during the execution, the script should show some information about that issue.
                                </li>
                            </ul>
                        </p>
                    </p>
                </p>

                <p id="integration-building">
                    <h3>Building Your Package</h3>

                    <p>
                        Please follow these steps to build your package.

                        <ol>
                            <li>
                                Create an empty folder which will contain package files (for example, <em>package</em>).

                                <div class="help-images">
                                    <a class="fancybox" title="Package Folder" href="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "integration", "file" => "1.png")); ?>"><img src="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "integration", "file" => "1-small.png")); ?>" alt="Package Folder"></a>
                                </div>
                            </li>
                            <li>
                                Create a package description file named <em>package.yaml</em> and fill it with required contents.

                                <div class="help-images">
                                    <a class="fancybox" title="Package Description File" href="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "integration", "file" => "2.png")); ?>"><img src="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "integration", "file" => "2-small.png")); ?>" alt="Package Description File"></a>
                                </div>
                            </li>
                            <li>
                                Put all required files into this folder (if you are doing the <em>Script Package</em>,
                                then don't forget to put an entry point file - <em>run.py</em> or <em>run.pl</em>).

                                <div class="help-images">
                                    <a class="fancybox" title="Package Files" href="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "integration", "file" => "3.png")); ?>"><img src="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "integration", "file" => "3-small.png")); ?>" alt="Package Files"></a>
                                </div>
                            </li>
                            <li>
                                ZIP your folder, so you will have a file named <em>package.zip</em> as a result.

                                <div class="help-images">
                                    <a class="fancybox" title="Compress Package" href="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "integration", "file" => "4.png")); ?>"><img src="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "integration", "file" => "4-small.png")); ?>" alt="Compress Package"></a>
                                    <a class="fancybox" title="Compressed Package" href="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "integration", "file" => "5.png")); ?>"><img src="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "integration", "file" => "5-small.png")); ?>" alt="Compressed Package"></a>
                                </div>
                            </li>
                            <li>
                                Go to <em>System &rarr; Scripts</em>, then choose the corresponding category
                                (<em>Libraries</em> or <em>Scripts</em>).

                                <div class="help-images">
                                    <a class="fancybox" title="Script Packages" href="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "integration", "file" => "6.png")); ?>"><img src="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "integration", "file" => "6-small.png")); ?>" alt="Script Packages"></a>
                                </div>
                            </li>
                            <li>
                                Press the <em>New Library</em> or <em>New Script</em> button and upload the package file.

                                <div class="help-images">
                                    <a class="fancybox" title="New Script" href="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "integration", "file" => "7.png")); ?>"><img src="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "integration", "file" => "7-small.png")); ?>" alt="New Script"></a>
                                    <a class="fancybox" title="New Script" href="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "integration", "file" => "8.png")); ?>"><img src="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "integration", "file" => "8-small.png")); ?>" alt="New Script"></a>
                                </div>
                            </li>
                            <li>
                                Go to the last page of package list and check the status of your package. It will take a while
                                until your package is installed.

                                <div class="help-images">
                                    <a class="fancybox" title="Installed Package" href="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "integration", "file" => "9.png")); ?>"><img src="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "integration", "file" => "9-small.png")); ?>" alt="Installed Package"></a>
                                </div>
                            </li>
                            <li>
                                Done! Now you can use your package in checks or other scripts!

                                <div class="help-images">
                                    <a class="fancybox" title="Use Package" href="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "integration", "file" => "10.png")); ?>"><img src="<?php echo $this->createUrl("app/file", array("section" => "help", "subsection" => "integration", "file" => "10-small.png")); ?>" alt="Use Package"></a>
                                </div>
                            </li>
                        </ol>
                    </p>
                </p>
            </p>
        </div>
    </div>
</div>

<script>
    $(function () {
        $(".fancybox").fancybox();
    });
</script>