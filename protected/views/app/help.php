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
                <li>
                    <a href="#guided">Guided Tests</a>

                    <ul>
                        <li><a href="#guided-creating">Creating Guided Test Checks</a></li>
                        <li><a href="#guided-performing">Performing Guided Tests</a></li>
                    </ul>
                </li>
                <li><a href="#integration">Integration Manual</a></li>
            </ul>

            <p id="whatsnew" class="section">
                <h2>What's New?</h2>

                <p>
                    Current version is <b>1.7.1</b>:

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
                            <a class="fancybox" title="Script Setup" href="/images/help/metasploit/1.png"><img src="/images/help/metasploit/1-small.png" alt="Script Setup"></a>
                        </div>

                        Then hit <em>Save</em>.
                    </li>
                    <li>
                        If you have used some additional arguments in your script (<b>@arg0</b>, <b>@arg1</b>, etc.), then you will need to add
                        corresponding inputs to the script. For our example script above you need to specify an additional input that will hold
                        login/password pairs. Please refer to metasploit documentation to find out the required file formats for each module.

                        <div class="help-images">
                            <a class="fancybox" title="Additional Arguments" href="/images/help/metasploit/2.png"><img src="/images/help/metasploit/2-small.png" alt="Additional Arguments"></a>
                            <a class="fancybox" title="Inputs" href="/images/help/metasploit/3.png"><img src="/images/help/metasploit/3-small.png" alt="Inputs"></a>
                        </div>
                    </li>
                    <li>
                        Now it's time to go to your project, use the check you just created and try to start the script.

                        <div class="help-images">
                            <a class="fancybox" title="Metasploit Run" href="/images/help/metasploit/4.png"><img src="/images/help/metasploit/4-small.png" alt="Metasploit Run"></a>
                        </div>
                    </li>
                </ol>
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
                                <a class="fancybox" title="Templates" href="/images/help/guided/1.png"><img src="/images/help/guided/1-small.png" alt="Templates"></a>
                            </div>
                        </li>
                        <li>
                            You see a list of Guided Test categories on this page. Press <em>New Category</em> button to create a new category.

                            <div class="help-images">
                                <a class="fancybox" title="Categories" href="/images/help/guided/2.png"><img src="/images/help/guided/2-small.png" alt="Categories"></a>
                            </div>
                        </li>
                        <li>
                            Enter the desired category name and hit <em>Save</em> button, then click on the <em>View</em> link for the created category (this link is in the top right corner of the page).

                            <div class="help-images">
                                <a class="fancybox" title="Category" href="/images/help/guided/3.png"><img src="/images/help/guided/3-small.png" alt="Category"></a>
                            </div>
                        </li>
                        <li>
                            Now you see a list of types for the category. You can create a new type by pressing the <em>New Type</em> button.

                            <div class="help-images">
                                <a class="fancybox" title="Types" href="/images/help/guided/4.png"><img src="/images/help/guided/4-small.png" alt="Types"></a>
                            </div>
                        </li>
                        <li>
                            On this page you should enter the desired type name and hit <em>Save</em>. After type is saved, please press the <em>View</em> link in the top right corner of the page.

                            <div class="help-images">
                                <a class="fancybox" title="Type" href="/images/help/guided/5.png"><img src="/images/help/guided/5-small.png" alt="Type"></a>
                            </div>
                        </li>
                        <li>
                            Now you are viewing a list of modules within the type. You can create a new module by hitting the <em>New Module</em> button.

                            <div class="help-images">
                                <a class="fancybox" title="Modules" href="/images/help/guided/6.png"><img src="/images/help/guided/6-small.png" alt="Modules"></a>
                            </div>
                        </li>
                        <li>
                            After creating a new module, please click the <em>View</em> link.

                            <div class="help-images">
                                <a class="fancybox" title="Module" href="/images/help/guided/7.png"><img src="/images/help/guided/7-small.png" alt="Module"></a>
                            </div>
                        </li>
                        <li>
                            Here you see a list of checks within this module. You can add a check by pressing <em>New Check</em> button.

                            <div class="help-images">
                                <a class="fancybox" title="Checks" href="/images/help/guided/8.png"><img src="/images/help/guided/8-small.png" alt="Checks"></a>
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
                                <a class="fancybox" title="Check" href="/images/help/guided/9.png"><img src="/images/help/guided/9-small.png" alt="Check"></a>
                            </div>
                        </li>
                        <li>If you selected some Dependency Processor for the current check, you will need to define some dependencies for it. Click on the <em>Dependencies</em> link on the top right corner.</li>
                        <li>
                            Now you are on the page with a list of dependencies for the current check. Click <em>New Dependency</em> to create a new dependency.

                            <div class="help-images">
                                <a class="fancybox" title="Dependencies" href="/images/help/guided/10.png"><img src="/images/help/guided/10-small.png" alt="Dependencies"></a>
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
                                <a class="fancybox" title="Dependency" href="/images/help/guided/11.png"><img src="/images/help/guided/11-small.png" alt="Dependency"></a>
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
                                <a class="fancybox" title="Project" href="/images/help/guided/12.png"><img src="/images/help/guided/12-small.png" alt="Project"></a>
                            </div>
                        </li>
                        <li>
                            You will see a module selector for the Guided Test. You should unfold the desired categories and types and select modules that you wish to run for this project.

                            <div class="help-images">
                                <a class="fancybox" title="Module Selector" href="/images/help/guided/13.png"><img src="/images/help/guided/13-small.png" alt="Module Selector"></a>
                            </div>
                        </li>
                        <li>
                            After you select modules, please press the <em>Save</em> button below. If you selected any modules, you will see that the <em>Start</em> button will appear next to the <em>Save</em> button. You should press it to start Guided Tests.

                            <div class="help-images">
                                <a class="fancybox" title="Save Module Selector" href="/images/help/guided/14.png"><img src="/images/help/guided/14-small.png" alt="Save Module Selector"></a>
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
                                <a class="fancybox" title="Check" href="/images/help/guided/15.png"><img src="/images/help/guided/15-small.png" alt="Check"></a>
                            </div>
                        </li>
                        <li>
                            Enter some test details here, as if you are on the usual check. In the example on the screenshots I’m filling in the Nmap TCP check, so I will be able to demonstrate you how check dependencies work. Please note, that if you want to use check/module dependencies you should set the <em>Extract</em> option of the <em>Nmap Port Scan</em> check. Otherwise check dependencies won’t work.

                            <div class="help-images">
                                <a class="fancybox" title="Check" href="/images/help/guided/16.png"><img src="/images/help/guided/16-small.png" alt="Check"></a>
                            </div>
                        </li>
                        <li>
                            After running the Nmap check, the check suggests  several additional modules and targets with open 22nd port. If you accept the suggestion, you should click on the ✔ icon, otherwise press the ✖ icon to delete the suggestion. After dealing with all suggested targets (if any), you should refresh the page and see if the system added any additional check modules to the current project. In our case, the system adds <em>Internal Penetration Test</em> module to the project with 1 check in it, so you can press <em>Next</em> button in the Guided Test Navigation menu.

                            <div class="help-images">
                                <a class="fancybox" title="Suggested Targets" href="/images/help/guided/17.png"><img src="/images/help/guided/17-small.png" alt="Suggested Targets"></a>
                            </div>
                        </li>
                        <li>
                            Here you will see that the system suggest you 2 additional targets. It also specifies the check that suggested these targets. If you wish, you can copy & paste these targets into the target field and perform all required checks, otherwise you can go to the check that suggested these targets and delete the suggestions.

                            <div class="help-images">
                                <a class="fancybox" title="Approved Suggested Targets" href="/images/help/guided/18.png"><img src="/images/help/guided/18-small.png" alt="Approved Suggested Targets"></a>
                            </div>
                        </li>
                    </ol>
                </p>

                <p>
                    Basically, that’s it. Currently Guided Test-based project do not support reports, because they use completely different database objects, but I will describe you this in the separate email.
                </p>
            </p>

            <p id="integration" class="section">
                <h2>Integration Manual</h2>

                <p>Coming soon (in 1.7.2)...</p>
            </p>
        </div>
    </div>
</div>

<script>
    $(function () {
        $(".fancybox").fancybox();
    });
</script>